<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api;

use Magento\MediaStorage\Model\File\Uploader;

/**
 * Provides additional functionality to upload the files to the file system of Magento 2
 */
interface FileUploaderManagementInterface
{
    /**
     * Upload the list of files
     *
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface[]
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upload(): array;

    /**
     * Get the media directory path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaDirectoryPath(): string;
}
