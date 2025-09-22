<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterface;
use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterfaceFactory;
use EPuzzle\FileUploader\Model\FileRepository\GetList;
use EPuzzle\FileUploader\Model\ResourceModel\File\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository\GetList
 * @covers \EPuzzle\FileUploader\Model\FileRepository\GetList::execute
 */
class GetListTest extends TestCase
{
    /** @var CollectionFactory&MockObject */
    private $collectionFactory;
    /** @var FileSearchResultsInterfaceFactory&MockObject */
    private $searchResultsFactory;
    /** @var CollectionProcessorInterface&MockObject */
    private $collectionProcessor;
    /** @var SearchCriteriaInterface&MockObject */
    private $searchCriteria;
    /** @var AbstractCollection&MockObject */
    private $collection;
    /** @var FileSearchResultsInterface&MockObject */
    private $searchResults;
    /** @var GetList */
    private $sut;

    protected function setUp(): void
    {
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactory = $this->getMockBuilder(
            FileSearchResultsInterfaceFactory::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionProcessor = $this->createMock(CollectionProcessorInterface::class);
        $this->searchCriteria = $this->createMock(SearchCriteriaInterface::class);
        $this->collection = $this->getMockBuilder(AbstractCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getItems', 'getSize'])
            ->getMockForAbstractClass();
        $this->searchResults = $this->createMock(FileSearchResultsInterface::class);
        $this->sut = new GetList(
            $this->collectionFactory,
            $this->searchResultsFactory,
            $this->collectionProcessor
        );
    }

    public function testExecutePopulatesSearchResultsWithProcessedCollection(): void
    {
        $itemA = new \stdClass();
        $itemB = new \stdClass();
        $itemsAssoc = [5 => $itemA, 9 => $itemB];
        $itemsSequential = [$itemA, $itemB];
        $total = 2;
        $this->collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collection);
        $this->collectionProcessor->expects($this->once())
            ->method('process')
            ->with($this->searchCriteria, $this->collection);
        $this->collection->expects($this->once())
            ->method('getItems')
            ->willReturn($itemsAssoc);
        $this->collection->expects($this->once())
            ->method('getSize')
            ->willReturn($total);
        $this->searchResultsFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->searchResults);
        $this->searchResults->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteria)
            ->willReturnSelf();
        $this->searchResults->expects($this->once())
            ->method('setItems')
            ->with($itemsSequential)
            ->willReturnSelf();
        $this->searchResults->expects($this->once())
            ->method('setTotalCount')
            ->with($total)
            ->willReturnSelf();
        $result = $this->sut->execute($this->searchCriteria);
        self::assertSame($this->searchResults, $result);
    }

    public function testExecuteHandlesEmptyCollection(): void
    {
        $total = 0;
        $this->collectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->collection);
        $this->collectionProcessor->expects($this->once())
            ->method('process')
            ->with($this->searchCriteria, $this->collection);
        $this->collection->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->collection->expects($this->once())
            ->method('getSize')
            ->willReturn($total);
        $this->searchResultsFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->searchResults);
        $this->searchResults->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteria)
            ->willReturnSelf();
        $this->searchResults->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->searchResults->expects($this->once())
            ->method('setTotalCount')
            ->with($total)
            ->willReturnSelf();
        $result = $this->sut->execute($this->searchCriteria);
        self::assertSame($this->searchResults, $result);
    }
}
