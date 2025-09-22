<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\File;

use EPuzzle\FileUploader\Model\File\Context;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Model\ActionValidator\RemoveAction;
use Magento\Framework\Model\Context as MagentoModelContext;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \EPuzzle\FileUploader\Model\File\Context
 * @covers \EPuzzle\FileUploader\Model\File\Context::getDirectoryList
 * @covers \EPuzzle\FileUploader\Model\File\Context::getFileDriver
 * @covers \EPuzzle\FileUploader\Model\File\Context::getStoreManager
 * @covers \EPuzzle\FileUploader\Model\File\Context::getIoFile
 */
class ContextTest extends TestCase
{
    /** @var LoggerInterface&MockObject */
    private $logger;
    /** @var ManagerInterface&MockObject */
    private $eventDispatcher;
    /** @var CacheInterface&MockObject */
    private $cacheManager;
    /** @var State&MockObject */
    private $appState;
    /** @var RemoveAction&MockObject */
    private $actionValidator;
    /** @var DirectoryList&MockObject */
    private $directoryList;
    /** @var DriverInterface&MockObject */
    private $fileDriver;
    /** @var StoreManagerInterface&MockObject */
    private $storeManager;
    /** @var IoFile&MockObject */
    private $ioFile;
    /** @var Context */
    private $sut;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(ManagerInterface::class);
        $this->cacheManager = $this->createMock(CacheInterface::class);
        $this->appState = $this->getMockBuilder(State::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->actionValidator = $this->getMockBuilder(RemoveAction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryList = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileDriver = $this->createMock(DriverInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->ioFile = $this->getMockBuilder(IoFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sut = new Context(
            $this->logger,
            $this->eventDispatcher,
            $this->cacheManager,
            $this->appState,
            $this->actionValidator,
            $this->directoryList,
            $this->fileDriver,
            $this->storeManager,
            $this->ioFile
        );
    }

    public function testExtendsMagentoModelContext(): void
    {
        self::assertInstanceOf(MagentoModelContext::class, $this->sut);
    }

    public function testGettersReturnInjectedDependencies(): void
    {
        self::assertSame($this->directoryList, $this->sut->getDirectoryList());
        self::assertSame($this->fileDriver, $this->sut->getFileDriver());
        self::assertSame($this->storeManager, $this->sut->getStoreManager());
        self::assertSame($this->ioFile, $this->sut->getIoFile());
    }
}
