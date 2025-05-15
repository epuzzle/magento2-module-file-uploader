<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\BuildUploader;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolverInterface;
use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File;

/**
 * Used to resolve files from the $_FILES
 */
readonly class Uploader implements FileResolverInterface
{
    /**
     * Uploader
     *
     * @param FileRepositoryInterface $fileRepository
     * @param GetMediaDirectoryPath $getMediaDirectoryPath
     * @param File $fileAdapter
     * @param BuildUploader $buildUploader
     */
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private GetMediaDirectoryPath $getMediaDirectoryPath,
        private File $fileAdapter,
        private BuildUploader $buildUploader
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        $pathToPaste = $this->getMediaDirectoryPath->execute();
        $pathToPaste .= DIRECTORY_SEPARATOR . 'uploader' . DIRECTORY_SEPARATOR;
        $this->fileAdapter->mkdir($pathToPaste);
        $files = [];
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        if (!empty($_FILES)) {
            $uploader = $this->buildUploader->execute();
            try {
                $fileData = $uploader->save($pathToPaste);
            } catch (Exception $exception) {
                throw new FileSystemException(__('Could not upload the file.'), $exception);
            }
            $file = $this->fileRepository->create();
            $file->setType($fileData['type']);
            $file->setName($fileData['name']);
            $file->setSize($fileData['size']);
            $file->setPath($fileData['path']);
            $file->setMediaUrl($fileData['url']);
            $this->fileRepository->save($file);
            $files[] = $file;
        }

        return $files;
    }
}
