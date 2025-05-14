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
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function upload(): Data\FileInterface;

    /**
     * Builds the uploader object
     *
     * @return \Magento\MediaStorage\Model\File\Uploader
     */
    public function buildUploader(): Uploader;

    /**
     * Get the media directory path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMediaDirectoryPath(): string;
}
