<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\File;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Model\ActionValidator\RemoveAction;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * The context class for the file entity
 */
class Context extends \Magento\Framework\Model\Context
{
    /**
     * Context
     *
     * @param LoggerInterface $logger
     * @param ManagerInterface $eventDispatcher
     * @param CacheInterface $cacheManager
     * @param State $appState
     * @param RemoveAction $actionValidator
     * @param DirectoryList $directoryList
     * @param DriverInterface $fileDriver
     * @param StoreManagerInterface $storeManager
     * @param IoFile $ioFile
     */
    public function __construct(
        LoggerInterface $logger,
        ManagerInterface $eventDispatcher,
        CacheInterface $cacheManager,
        State $appState,
        RemoveAction $actionValidator,
        private readonly DirectoryList $directoryList,
        private readonly DriverInterface $fileDriver,
        private readonly StoreManagerInterface $storeManager,
        private readonly IoFile $ioFile
    ) {
        parent::__construct(
            $logger,
            $eventDispatcher,
            $cacheManager,
            $appState,
            $actionValidator
        );
    }

    /**
     * Gets the directory list
     *
     * @return DirectoryList
     */
    public function getDirectoryList(): DirectoryList
    {
        return $this->directoryList;
    }

    /**
     * Gets the file driver
     *
     * @return DriverInterface
     */
    public function getFileDriver(): DriverInterface
    {
        return $this->fileDriver;
    }

    /**
     * Gets the store manager
     *
     * @return StoreManagerInterface|StoreManager
     */
    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * Gets the io file
     *
     * @return IoFile
     */
    public function getIoFile(): IoFile
    {
        return $this->ioFile;
    }
}
