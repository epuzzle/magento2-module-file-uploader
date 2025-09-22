<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\ResourceModel;

use EPuzzle\FileUploader\Model\ResourceModel\File;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\ResourceModel\File
 */
class FileTest extends TestCase
{
    /**
     * @var TransactionManagerInterface&MockObject
     */
    private $transactionManager;
    /**
     * @var ObjectRelationProcessor&MockObject
     */
    private $objectRelationProcessor;

    protected function setUp(): void
    {
        $this->transactionManager = $this->createMock(TransactionManagerInterface::class);
        $this->objectRelationProcessor = $this->getMockBuilder(ObjectRelationProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructInitializesTableAndPrimaryKey(): void
    {
        $resources = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resources->method('getTableName')
            ->willReturnCallback(static function (string $name): string {
                return $name;
            });
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getTransactionManager')->willReturn($this->transactionManager);
        $context->method('getResources')->willReturn($resources);
        $context->method('getObjectRelationProcessor')->willReturn($this->objectRelationProcessor);
        $sut = new class ($context) extends File {
            public function mainTable(): ?string
            {
                return $this->getMainTable();
            }
        };
        self::assertInstanceOf(AbstractDb::class, $sut);
        self::assertSame(File::TABLE_NAME, $sut->mainTable());
        self::assertSame(File::PK, $sut->getIdFieldName());
    }

    public function testGetConnectionUsesDefaultWhenNameIsNull(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $resources = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resources->method('getTableName')
            ->willReturnCallback(static function (string $name): string {
                return $name;
            });
        $resources->expects($this->atLeastOnce())
            ->method('getConnection')
            ->with('default')
            ->willReturn($adapter);
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getTransactionManager')->willReturn($this->transactionManager);
        $context->method('getResources')->willReturn($resources);
        $context->method('getObjectRelationProcessor')->willReturn($this->objectRelationProcessor);
        $sut = new File($context);
        $first = $sut->getConnection();
        $second = $sut->getConnection();
        self::assertSame($adapter, $first);
        self::assertSame($adapter, $second);
    }

    public function testGetConnectionUsesProvidedConnectionName(): void
    {
        $adapter = $this->createMock(AdapterInterface::class);
        $resources = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resources->method('getTableName')
            ->willReturnCallback(static function (string $name): string {
                return $name;
            });
        $resources->expects($this->atLeastOnce())
            ->method('getConnection')
            ->with('custom')
            ->willReturn($adapter);
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getTransactionManager')->willReturn($this->transactionManager);
        $context->method('getResources')->willReturn($resources);
        $context->method('getObjectRelationProcessor')->willReturn($this->objectRelationProcessor);
        $sut = new File($context, 'custom');
        $result = $sut->getConnection();
        self::assertSame($adapter, $result);
    }
}
