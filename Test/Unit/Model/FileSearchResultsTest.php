<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterface;
use EPuzzle\FileUploader\Model\FileSearchResults;
use Magento\Framework\Api\SearchResults;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileSearchResults::getItems
 * @covers \EPuzzle\FileUploader\Model\FileSearchResults::setItems
 */
class FileSearchResultsTest extends TestCase
{
    /**
     * @var FileSearchResults
     */
    private $sut;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->sut = new FileSearchResults();
        self::assertInstanceOf(SearchResults::class, $this->sut);
        self::assertInstanceOf(FileSearchResultsInterface::class, $this->sut);
    }

    public function testSetItemsAndGetItemsReturnSameArray(): void
    {
        /** @var FileInterface&MockObject $f1 */
        $f1 = $this->createMock(FileInterface::class);
        /** @var FileInterface&MockObject $f2 */
        $f2 = $this->createMock(FileInterface::class);
        $items = [$f1, $f2];
        $this->sut->setItems($items);
        $result = $this->sut->getItems();
        self::assertSame($items, $result);
    }

    public function testSetItemsTwiceOverwritesPreviousItems(): void
    {
        /** @var FileInterface&MockObject $f1 */
        $f1 = $this->createMock(FileInterface::class);
        /** @var FileInterface&MockObject $f2 */
        $f2 = $this->createMock(FileInterface::class);
        $this->sut->setItems([$f1]);
        self::assertSame([$f1], $this->sut->getItems());
        $this->sut->setItems([$f2]);
        self::assertSame([$f2], $this->sut->getItems());
    }
}
