<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api;

/**
 * Provides additional functionality to upload the files to the file system of Magento 2
 */
interface FileUploaderManagementInterface
{
    /**
     * Uploads the list of files
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface|null $settings
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface[]
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upload(?Data\FileUploaderSettingsInterface $settings = null): array;

    /**
     * Gets the media directory path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaDirectoryPath(): string;

    /**
     * Creates the file uploader settings entity
     *
     * @return \EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface
     */
    public function createFileUploaderSettings(): Data\FileUploaderSettingsInterface;
}
