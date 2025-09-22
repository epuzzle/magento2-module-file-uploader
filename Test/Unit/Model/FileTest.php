<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model;

use EPuzzle\FileUploader\Model\File;
use EPuzzle\FileUploader\Model\File\Context;
use EPuzzle\FileUploader\Model\ResourceModel\File as ResourceModelFile;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as DbAbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var (Context&MockObject)|MockObject
     */
    private $context;
    /**
     * @var (DirectoryList&MockObject)|MockObject
     */
    private $directoryList;
    /**
     * @var (FileDriver&MockObject)|MockObject
     */
    private $fileDriver;
    /**
     * @var (IoFile&MockObject)|MockObject
     */
    private $ioFile;
    /**
     * @var (StoreManagerInterface&MockObject)|MockObject
     */
    private $storeManager;
    /**
     * @var (Store&MockObject)|MockObject
     */
    private $store;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var (DbAbstractResource&MockObject)|MockObject
     */
    private $resource;
    /**
     * @var (ManagerInterface&MockObject)|ManagerInterface
     */
    private $eventManager;
    /**
     * @var File
     */
    private $sut;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryList = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileDriver = $this->getMockBuilder(FileDriver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->ioFile = $this->getMockBuilder(IoFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->store = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventManager = $this->createMock(ManagerInterface::class);
        $this->context->method('getDirectoryList')->willReturn($this->directoryList);
        $this->context->method('getFileDriver')->willReturn($this->fileDriver);
        $this->context->method('getIoFile')->willReturn($this->ioFile);
        $this->context->method('getStoreManager')->willReturn($this->storeManager);
        $this->context->method('getEventDispatcher')->willReturn($this->eventManager);
        $this->registry = new Registry();
        $this->resource = $this->getMockBuilder(DbAbstractResource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_construct', 'getConnection', 'getIdFieldName'])
            ->getMock();
        $this->resource->method('getIdFieldName')->willReturn(ResourceModelFile::PK);
        $this->sut = new File(
            $this->context,
            $this->registry,
            $this->resource,
            null,
            []
        );
        $this->sut->_construct();
    }

    public function testConstructSetsIdFieldName(): void
    {
        self::assertSame(ResourceModelFile::PK, $this->sut->getIdFieldName());
    }

    public function testGetAndSetEntityIdCastsToInt(): void
    {
        $this->sut->setEntityId('123');
        self::assertSame(123, $this->sut->getEntityId());
    }

    public function testGetSetSimpleFields(): void
    {
        $this->sut->setName('file.jpg');
        $this->sut->setPath('/tmp/file.jpg');
        $this->sut->setRelativePath('catalog/product/');
        $this->sut->setSize(42);
        $this->sut->setType('image/jpeg');
        $this->sut->setExtension('jpg');
        self::assertSame('file.jpg', $this->sut->getName());
        self::assertSame('/tmp/file.jpg', $this->sut->getPath());
        self::assertSame('catalog/product/', $this->sut->getRelativePath());
        self::assertSame(42, $this->sut->getSize());
        self::assertSame('image/jpeg', $this->sut->getType());
        self::assertSame('jpg', $this->sut->getExtension());
    }

    public function testGetFullPathSuccess(): void
    {
        $root = '/app';
        $this->sut->setName('foo.jpg');
        $this->sut->setRelativePath('pub/media/catalog');
        $this->directoryList->expects($this->once())
            ->method('getRoot')
            ->willReturn($root);
        $expectedParam = $root . DIRECTORY_SEPARATOR
            . 'pub/media/catalog' . DIRECTORY_SEPARATOR . 'foo.jpg';
        $this->fileDriver->expects($this->once())
            ->method('getRealPath')
            ->with($expectedParam)
            ->willReturn('/app/pub/media/catalog/foo.jpg');
        self::assertSame('/app/pub/media/catalog/foo.jpg', $this->sut->getFullPath());
    }

    public function testGetFullPathThrowsIfNotFound(): void
    {
        $this->sut->setName('missing.jpg');
        $this->sut->setRelativePath('var');
        $this->directoryList->method('getRoot')->willReturn('/root');
        $this->fileDriver->method('getRealPath')->willReturn(false);
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('The file is not found.');
        $this->sut->getFullPath();
    }

    public function testGetMediaUrlSuccess(): void
    {
        $this->sut->setName('f.jpg');
        $this->sut->setRelativePath('pub/media/catalog');
        $this->directoryList->method('getRoot')->willReturn('/app');
        $this->fileDriver->method('getRealPath')
            ->willReturn('/app/pub/media/catalog/f.jpg');
        $this->directoryList->expects($this->once())
            ->method('getPath')
            ->with(DirectoryList::MEDIA)
            ->willReturn('/app/pub/media');
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($this->store);
        $this->store->expects($this->once())
            ->method('getBaseUrl')
            ->with(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            ->willReturn('https://example.com/media/');
        $result = $this->sut->getMediaUrl();
        self::assertSame('https://example.com/media/catalog/f.jpg', $result);
    }

    public function testGetMediaUrlReturnsNullOnException(): void
    {
        $this->sut->setName('x.jpg');
        $this->sut->setRelativePath('pub/media');
        $this->directoryList->method('getRoot')->willReturn('/app');
        $this->fileDriver->method('getRealPath')
            ->willThrowException(new FileSystemException(__('fail')));
        self::assertNull($this->sut->getMediaUrl());
    }

    public function testBeforeSaveGeneratesIdentifierRelativePathAndExtension(): void
    {
        $root = '/app';
        $absPath = '/app/pub/media/catalog/p/';
        $this->sut->setName('file.JPG');
        $this->sut->setPath($absPath);
        $this->sut->setType('image/jpeg');
        $this->sut->setSize(100);
        $this->directoryList->expects($this->any())
            ->method('getRoot')
            ->willReturn($root);
        $this->fileDriver->expects($this->exactly(2))
            ->method('getRealPath')
            ->willReturnOnConsecutiveCalls($absPath, $absPath);
        $this->ioFile->expects($this->once())
            ->method('getPathInfo')
            ->with($absPath)
            ->willReturn(['extension' => 'JPG']);
        $this->sut->beforeSave();
        self::assertNotSame('', $this->sut->getIdentifier());
        self::assertSame('pub/media/catalog/p' . DIRECTORY_SEPARATOR, $this->sut->getRelativePath());
        self::assertSame('jpg', $this->sut->getExtension());
    }

    public function testBeforeSaveDoesNotOverrideExistingValues(): void
    {
        $this->sut->setRelativePath('rel' . DIRECTORY_SEPARATOR);
        $this->sut->setType('type/x');
        $this->sut->setSize(7);
        $this->sut->setExtension('ext');
        $this->directoryList->expects($this->once())->method('getRoot')->willReturn('/any');
        $this->fileDriver->expects($this->once())
            ->method('getRealPath')
            ->willReturn('/any/rel/file.ext');
        $this->ioFile->expects($this->never())->method('getPathInfo');
        $this->sut->setName('file.ext');
        $this->sut->beforeSave();
        self::assertSame('rel' . DIRECTORY_SEPARATOR, $this->sut->getRelativePath());
        self::assertSame('type/x', $this->sut->getType());
        self::assertSame(7, $this->sut->getSize());
        self::assertSame('ext', $this->sut->getExtension());
    }

    public function testNoopSettersDoNotChangeData(): void
    {
        $this->sut->setMediaUrl('ignored');
        $this->sut->setFullPath('/ignored');
        $this->sut->setIdentifier('ignored');
        self::assertSame('', $this->sut->getIdentifier());
        self::assertSame('', $this->sut->getMediaUrl() ?? '');
    }
}
