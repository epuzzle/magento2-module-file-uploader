<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterfaceFactory;
use EPuzzle\FileUploader\Api\FileResolverInterface;
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
     * @param FileResolverInterface $fileResolver
     * @param FileUploaderSettingsInterfaceFactory $fileUploaderSettingsFactory
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private FileUploaderManagement\GetMediaDirectoryPath $getMediaDirectoryPath,
        private FileResolverInterface $fileResolver,
        private FileUploaderSettingsInterfaceFactory $fileUploaderSettingsFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function upload(?FileUploaderSettingsInterface $settings = null): array
    {
        $settings = $settings ?: $this->createFileUploaderSettings();

        return $this->fileResolver->resolve($settings);
    }

    /**
     * @inheritDoc
     */
    public function getMediaDirectoryPath(): string
    {
        return $this->getMediaDirectoryPath->execute();
    }

    /**
     * @inheritDoc
     */
    public function createFileUploaderSettings(): Data\FileUploaderSettingsInterface
    {
        return $this->fileUploaderSettingsFactory->create();
    }
}
