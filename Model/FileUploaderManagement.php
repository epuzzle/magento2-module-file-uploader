<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\FileUploaderManagementInterface;

/**
 * Provides additional functionality to upload the files to the file system of Magento 2
 */
class FileUploaderManagement implements FileUploaderManagementInterface
{
    /**
     * FileUploaderManagement
     *
     * @param FileUploaderManagement\GetMediaDirectoryPath $getMediaDirectoryPath
     * @param FileUploaderManagement\Upload\FileResolverInterface $fileResolver
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private FileUploaderManagement\GetMediaDirectoryPath $getMediaDirectoryPath,
        private FileUploaderManagement\Upload\FileResolverInterface $fileResolver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upload(): array
    {
        return $this->fileResolver->resolve();
    }

    /**
     * @inheritDoc
     */
    public function getMediaDirectoryPath(): string
    {
        return $this->getMediaDirectoryPath->execute();
    }
}
