<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use EPuzzle\FileUploader\Model\ConfigProvider;

/**
 * Used to get the media directory path (full path)
 */
class GetMediaDirectoryPath
{
    /**
     * GetMediaDirectoryPath
     *
     * @param Filesystem $filesystem
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly ConfigProvider $configProvider
    ) {
    }

    /**
     * Gets the media directory path (full path)
     *
     * @return string
     * @throws FileSystemException
     */
    public function execute(): string
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        return $mediaDirectory->getAbsolutePath($this->configProvider->getMediaDirectory());
    }
}
