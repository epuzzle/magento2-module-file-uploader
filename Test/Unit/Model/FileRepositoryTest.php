<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Model\FileRepository;
use EPuzzle\FileUploader\Model\FileRepository\Create;
use EPuzzle\FileUploader\Model\FileRepository\Delete;
use EPuzzle\FileUploader\Model\FileRepository\Get;
use EPuzzle\FileUploader\Model\FileRepository\GetList;
use EPuzzle\FileUploader\Model\FileRepository\Save;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository
 */
class FileRepositoryTest extends TestCase
{
    /**
     * @var Get&MockObject
     */
    private $get;
    /**
     * @var Save&MockObject
     */
    private $save;
    /**
     * @var Delete&MockObject
     */
    private $delete;
    /**
     * @var GetList&MockObject
     */
    private $getList;
    /**
     * @var Create&MockObject
     */
    private $create;
    /**
     * @var SearchCriteriaBuilderFactory&MockObject
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var FileRepositoryInterface
     */
    private $sut;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->get = $this->getMockBuilder(Get::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->save = $this->getMockBuilder(Save::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->delete = $this->getMockBuilder(Delete::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getList = $this->getMockBuilder(GetList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->create = $this->getMockBuilder(Create::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderFactory = $this->getMockBuilder(SearchCriteriaBuilderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sut = new FileRepository(
            $this->get,
            $this->save,
            $this->delete,
            $this->getList,
            $this->create,
            $this->searchCriteriaBuilderFactory
        );
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::get
     */
    public function testGetReturnsFile(): void
    {
        $file = $this->createMock(FileInterface::class);
        $this->get->expects($this->once())
            ->method('execute')
            ->with(10)
            ->willReturn($file);
        $result = $this->sut->get(10);
        self::assertSame($file, $result);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::save
     */
    public function testSaveDelegatesAndReturnsId(): void
    {
        $file = $this->createMock(FileInterface::class);
        $this->save->expects($this->once())
            ->method('execute')
            ->with($file)
            ->willReturn(55);
        $result = $this->sut->save($file);
        self::assertSame(55, $result);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::delete
     */
    public function testDeleteDelegates(): void
    {
        $file = $this->createMock(FileInterface::class);
        $this->delete->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($file));
        $this->sut->delete($file);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::deleteById
     */
    public function testDeleteByIdLoadsThenDeletes(): void
    {
        $file = $this->createMock(FileInterface::class);
        $this->get->expects($this->once())
            ->method('execute')
            ->with(7)
            ->willReturn($file);
        $this->delete->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($file));
        $this->sut->deleteById(7);
        $this->addToAssertionCount(1);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::getList
     */
    public function testGetListReturnsSearchResults(): void
    {
        $criteria = $this->createMock(SearchCriteriaInterface::class);
        $results = $this->createMock(FileSearchResultsInterface::class);
        $this->getList->expects($this->once())
            ->method('execute')
            ->with($criteria)
            ->willReturn($results);
        $result = $this->sut->getList($criteria);
        self::assertSame($results, $result);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::create
     */
    public function testCreateReturnsNewFile(): void
    {
        $file = $this->createMock(FileInterface::class);
        $this->create->expects($this->once())
            ->method('execute')
            ->willReturn($file);
        $result = $this->sut->create();
        self::assertSame($file, $result);
    }

    /**
     * @covers \EPuzzle\FileUploader\Model\FileRepository::createSearchCriteriaBuilder
     */
    public function testCreateSearchCriteriaBuilderReturnsBuilderFromFactory(): void
    {
        $builder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($builder);
        $result = $this->sut->createSearchCriteriaBuilder();
        self::assertSame($builder, $result);
    }
}
