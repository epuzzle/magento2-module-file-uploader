<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\Io\File;

/**
 * Used to resolve all external links files
 */
class ExternalLink implements FileResolverInterface
{
    /**
     * ExternalLink
     *
     * @param RequestInterface $request
     * @param FileRepositoryInterface $fileRepository
     * @param GetMediaDirectoryPath $getMediaDirectoryPath
     * @param File $fileAdapter
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly FileRepositoryInterface $fileRepository,
        private readonly GetMediaDirectoryPath $getMediaDirectoryPath,
        private readonly File $fileAdapter,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(FileUploaderSettingsInterface $settings): array
    {
        $externalLinks = $this->request->getParam('external_links') ?? [];
        if (method_exists($this->request, 'getContent')
            && ($content = (string)$this->request->getContent())) {
            $content = json_decode($content, true);
            $externalLinks = array_merge($externalLinks, $content['external_links'] ?? []);
        }
        $files = [];
        if (!empty($externalLinks)) {
            $pathToPaste = $settings->getExtensionAttributes()->getPathToPaste();
            if (!$pathToPaste) {
                $pathToPaste = $this->getMediaDirectoryPath->execute();
                $pathToPaste .= DIRECTORY_SEPARATOR . 'external_links' . DIRECTORY_SEPARATOR;
            }
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
                $this->fileRepository->save($file);
                $files[] = $file;
            }
        }

        return $files;
    }
}
