<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Model\ConfigProvider;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetVarDirectoryPath;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\GetVarDirectoryPath
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\GetVarDirectoryPath::execute
 */
class GetVarDirectoryPathTest extends TestCase
{
    /** @var Filesystem&MockObject */
    private $filesystem;
    /** @var ConfigProvider&MockObject */
    private $configProvider;
    /** @var WriteInterface&MockObject */
    private $directoryWrite;
    /** @var GetVarDirectoryPath */
    private $sut;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->directoryWrite = $this->createMock(WriteInterface::class);
        $this->sut = new GetVarDirectoryPath(
            $this->filesystem,
            $this->configProvider
        );
    }

    public function testExecuteReturnsAbsolutePath(): void
    {
        $varDirName = 'myvar';
        $absolutePath = '/var/www/var/myvar/';
        $this->configProvider->expects($this->once())
            ->method('getVarDirectory')
            ->willReturn($varDirName);
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->directoryWrite);
        $this->directoryWrite->expects($this->once())
            ->method('getAbsolutePath')
            ->with($varDirName)
            ->willReturn($absolutePath);
        $result = $this->sut->execute();
        self::assertSame($absolutePath, $result);
    }

    public function testExecutePropagatesExceptionFromGetDirectoryWrite(): void
    {
        $this->configProvider->expects($this->never())->method('getVarDirectory');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willThrowException(new FileSystemException(__('fs fail')));
        $this->expectException(FileSystemException::class);
        $this->sut->execute();
    }

    public function testExecutePropagatesExceptionFromGetAbsolutePath(): void
    {
        $varDirName = 'logs';
        $this->configProvider->expects($this->once())
            ->method('getVarDirectory')
            ->willReturn($varDirName);
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->directoryWrite);
        $this->directoryWrite->expects($this->once())
            ->method('getAbsolutePath')
            ->with($varDirName)
            ->willThrowException(new FileSystemException(__('path fail')));
        $this->expectException(FileSystemException::class);
        $this->sut->execute();
    }
}
