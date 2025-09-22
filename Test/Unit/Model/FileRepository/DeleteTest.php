<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileRepository;

use EPuzzle\FileUploader\Model\File as FileModel;
use EPuzzle\FileUploader\Model\FileRepository\Delete;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Delete
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Delete::execute
 */
class DeleteTest extends TestCase
{
    /** @var Resource&MockObject */
    private $resource;
    /** @var FileModel&MockObject */
    private $file;
    /** @var Delete */
    private $sut;

    protected function setUp(): void
    {
        $this->resource = $this->getMockBuilder(Resource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();
        $this->file = $this->getMockBuilder(FileModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityId'])
            ->getMock();
        $this->sut = new Delete($this->resource);
    }

    public function testExecuteDeletesFileAndReturnsEntityId(): void
    {
        $this->file->method('getEntityId')->willReturn(123);
        $this->resource->expects($this->once())
            ->method('delete')
            ->with($this->file);
        $result = $this->sut->execute($this->file);
        self::assertSame(123, $result);
    }

    public function testExecuteThrowsCouldNotDeleteExceptionOnFailure(): void
    {
        $this->file->method('getEntityId')->willReturn(456);
        $this->resource->expects($this->once())
            ->method('delete')
            ->willThrowException(new Exception('fail'));
        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('Could not delete the file entity');
        $this->sut->execute($this->file);
    }
}
