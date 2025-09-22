<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Model\ConfigProvider;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath::execute
 */
class GetMediaDirectoryPathTest extends TestCase
{
    /** @var Filesystem&MockObject */
    private $filesystem;
    /** @var ConfigProvider&MockObject */
    private $configProvider;
    /** @var WriteInterface&MockObject */
    private $directoryWrite;
    /** @var GetMediaDirectoryPath */
    private $sut;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->directoryWrite = $this->createMock(WriteInterface::class);
        $this->sut = new GetMediaDirectoryPath(
            $this->filesystem,
            $this->configProvider
        );
    }

    public function testExecuteReturnsAbsolutePath(): void
    {
        $mediaDirName = 'uploads';
        $absolutePath = '/var/www/pub/media/uploads/';
        $this->configProvider->expects($this->once())
            ->method('getMediaDirectory')
            ->willReturn($mediaDirName);
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->directoryWrite);
        $this->directoryWrite->expects($this->once())
            ->method('getAbsolutePath')
            ->with($mediaDirName)
            ->willReturn($absolutePath);
        $result = $this->sut->execute();
        self::assertSame($absolutePath, $result);
    }

    public function testExecutePropagatesExceptionFromGetDirectoryWrite(): void
    {
        $this->configProvider->expects($this->never())->method('getMediaDirectory');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willThrowException(new FileSystemException(__('fs fail')));
        $this->expectException(FileSystemException::class);
        $this->sut->execute();
    }

    public function testExecutePropagatesExceptionFromGetAbsolutePath(): void
    {
        $mediaDirName = 'uploads';
        $this->configProvider->expects($this->once())
            ->method('getMediaDirectory')
            ->willReturn($mediaDirName);
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->willReturn($this->directoryWrite);
        $this->directoryWrite->expects($this->once())
            ->method('getAbsolutePath')
            ->with($mediaDirName)
            ->willThrowException(new FileSystemException(__('path fail')));
        $this->expectException(FileSystemException::class);
        $this->sut->execute();
    }
}
