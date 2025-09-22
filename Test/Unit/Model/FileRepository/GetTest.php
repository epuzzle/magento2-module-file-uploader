<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileRepository;

use EPuzzle\FileUploader\Model\File as FileModel;
use EPuzzle\FileUploader\Model\FileRepository\Create;
use EPuzzle\FileUploader\Model\FileRepository\Get;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Get
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Get::execute
 */
class GetTest extends TestCase
{
    /** @var Resource&MockObject */
    private $resource;
    /** @var Create&MockObject */
    private $create;
    /** @var FileModel&MockObject */
    private $file;
    /** @var Get */
    private $sut;

    protected function setUp(): void
    {
        $this->resource = $this->getMockBuilder(Resource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['load'])
            ->getMock();
        $this->create = $this->createMock(Create::class);
        $this->file = $this->getMockBuilder(FileModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityId'])
            ->getMock();
        $this->sut = new Get($this->resource, $this->create);
    }

    public function testExecuteLoadsAndReturnsFile(): void
    {
        $fileId = 10;
        $this->create->expects($this->once())
            ->method('execute')
            ->willReturn($this->file);
        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->file, $fileId);
        $this->file->method('getEntityId')->willReturn($fileId);
        $result = $this->sut->execute($fileId);
        self::assertSame($this->file, $result);
    }

    public function testExecuteThrowsWhenFileNotFound(): void
    {
        $fileId = 404;
        $this->create->expects($this->once())
            ->method('execute')
            ->willReturn($this->file);
        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->file, $fileId);
        $this->file->method('getEntityId')->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('File with id "404" does not exist.');
        $this->sut->execute($fileId);
    }
}
