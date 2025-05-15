<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolverInterface;
use InvalidArgumentException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;

/**
 * Used to resolve all external links files
 */
readonly class ExternalLink implements FileResolverInterface
{
    /**
     * ExternalLink
     *
     * @param RequestInterface $request
     * @param FileRepositoryInterface $fileRepository
     * @param GetMediaDirectoryPath $getMediaDirectoryPath
     * @param File $fileAdapter
     * @param UrlInterface $url
     */
    public function __construct(
        private RequestInterface $request,
        private FileRepositoryInterface $fileRepository,
        private GetMediaDirectoryPath $getMediaDirectoryPath,
        private File $fileAdapter,
        private UrlInterface $url,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        $externalLinks = $this->request->getParam('external_links') ?? [];
        if ($content = (string)$this->request->getContent()) {
            $content = json_decode($content, true);
            $externalLinks = array_merge($externalLinks, $content['external_links'] ?? []);
        }
        $files = [];
        if (!empty($externalLinks)) {
            $pathToPaste = $this->getMediaDirectoryPath->execute()
                . DIRECTORY_SEPARATOR
                . 'external_links'
                . DIRECTORY_SEPARATOR;
            $this->fileAdapter->mkdir($pathToPaste);
            foreach ($externalLinks as $externalLink) {
                $file = $this->fileRepository->create();
                $fileName = $this->fileAdapter->getPathInfo($externalLink)['basename'];
                // The MD5 here is not for crypto cases
                // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
                $fileName = md5($externalLink) . '_' . $fileName;
                $filePath = $pathToPaste . $fileName;
                if (!$this->fileAdapter->fileExists($filePath)) {
                    if (false === $this->fileAdapter->read($externalLink, $filePath)) {
                        throw new NotFoundException(__('Failed to download file from URL.'));
                    }
                }
                $file->setName($fileName);
                $file->setPath($pathToPaste);
                $file->setType(mime_content_type($filePath));
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                $file->setSize(filesize($filePath));
                $file->setPath($pathToPaste);
                $file->setMediaUrl($this->getMediaUrlFromPath($filePath));
                $this->fileRepository->save($file);
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Get media URL form the path
     *
     * @param string $absolutePath
     * @return string
     */
    private function getMediaUrlFromPath(string $absolutePath): string
    {
        // Look for /pub/media or /media in the path
        if (preg_match('#/(pub/)?media/(.+)$#', str_replace('\\', '/', $absolutePath), $matches)) {
            $relativePath = $matches[2];

            return $this->url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                . ltrim($relativePath, '/');
        }
        throw new InvalidArgumentException("Provided path is not inside media directory.");
    }
}
