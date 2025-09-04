<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Model\ConfigProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

/**
 * Used to get the var directory path (full path)
 */
class GetVarDirectoryPath
{
    /**
     * GetVarDirectoryPath
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
     * Gets the var directory path (full path)
     *
     * @return string
     * @throws FileSystemException
     */
    public function execute(): string
    {
        $varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

        return $varDirectory->getAbsolutePath($this->configProvider->getVarDirectory());
    }
}
