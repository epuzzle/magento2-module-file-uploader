<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileInterfaceFactory;
use EPuzzle\FileUploader\Model\FileRepository\Create;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Create
 * @covers \EPuzzle\FileUploader\Model\FileRepository\Create::execute
 */
class CreateTest extends TestCase
{
    /** @var FileInterfaceFactory&MockObject */
    private $fileFactory;
    /** @var FileInterface&MockObject */
    private $file;
    /** @var Create */
    private $sut;

    protected function setUp(): void
    {
        $this->fileFactory = $this->createMock(FileInterfaceFactory::class);
        $this->file = $this->createMock(FileInterface::class);
        $this->sut = new Create($this->fileFactory);
    }

    public function testExecuteReturnsCreatedFile(): void
    {
        $this->fileFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->file);
        $result = $this->sut->execute();
        self::assertSame($this->file, $result);
    }
}
