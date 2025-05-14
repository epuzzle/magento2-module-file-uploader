<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Api\FileUploaderManagementInterface;
use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\MediaStorage\Model\File\Uploader;

/**
 * Provides additional functionality to upload the files to the file system of Magento 2
 */
class FileUploaderManagement implements FileUploaderManagementInterface
{
    /**
     * FileUploaderManagement
     *
     * @param FileUploaderManagement\BuildUploader $buildUploader
     * @param FileUploaderManagement\GetMediaDirectoryPath $getMediaDirectoryPath
     * @param FileRepositoryInterface $fileRepository
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private readonly FileUploaderManagement\BuildUploader $buildUploader,
        private readonly FileUploaderManagement\GetMediaDirectoryPath $getMediaDirectoryPath,
        private readonly FileRepositoryInterface $fileRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upload(): FileInterface
    {
        try {
            $pathToPaste = $this->getMediaDirectoryPath();
            $uploader = $this->buildUploader();
            $fileData = $uploader->save($pathToPaste);
            $file = $this->fileRepository->create();
            $file->setType($fileData['type']);
            $file->setName($fileData['name']);
            $file->setSize($fileData['size']);
            $file->setPath($fileData['path']);
            $file->setMediaUrl($fileData['url']);
            $this->fileRepository->save($file);

            return $file;
        } catch (Exception $exception) {
            throw new FileSystemException(
                __(
                    'An error occurred while uploading your file: %error.',
                    ['error' => $exception->getMessage()]
                ),
                $exception
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function buildUploader(): Uploader
    {
        return $this->buildUploader->execute();
    }

    /**
     * @inheritDoc
     */
    public function getMediaDirectoryPath(): string
    {
        return $this->getMediaDirectoryPath->execute();
    }
}
