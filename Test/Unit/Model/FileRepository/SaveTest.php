<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileRepository;

use EPuzzle\FileUploader\Model\File as FileModel;
use EPuzzle\FileUploader\Model\FileRepository\Save;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Save
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Save::execute
 */
class SaveTest extends TestCase
{
    /** @var Resource&MockObject */
    private $resource;
    /** @var FileModel&MockObject */
    private $file;
    /** @var Save */
    private $sut;

    protected function setUp(): void
    {
        $this->resource = $this->getMockBuilder(Resource::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();
        $this->file = $this->getMockBuilder(FileModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityId'])
            ->getMock();
        $this->sut = new Save($this->resource);
    }

    public function testExecuteSavesFileAndReturnsEntityId(): void
    {
        $this->file->method('getEntityId')->willReturn(321);
        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->file);
        $result = $this->sut->execute($this->file);
        self::assertSame(321, $result);
    }

    public function testExecuteThrowsCouldNotSaveExceptionOnFailure(): void
    {
        $this->file->method('getEntityId')->willReturn(0);
        $this->resource->expects($this->once())
            ->method('save')
            ->with($this->file)
            ->willThrowException(new Exception('fail'));
        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('Could not save the file entity');
        $this->sut->execute($this->file);
    }
}
