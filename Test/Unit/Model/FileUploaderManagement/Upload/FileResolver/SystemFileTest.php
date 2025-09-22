<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetVarDirectoryPath;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\SystemFile;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File as IoFile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\SystemFile
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\SystemFile::resolve
 */
class SystemFileTest extends TestCase
{
    /** @var FileRepositoryInterface&MockObject */
    private $fileRepository;
    /** @var GetVarDirectoryPath&MockObject */
    private $getVarDirectoryPath;
    /** @var IoFile&MockObject */
    private $io;
    /** @var FileDriver&MockObject */
    private $fileDriver;
    /** @var FileUploaderSettingsInterface&MockObject */
    private $settings;
    /** @var FileUploaderSettingsExtensionInterface&MockObject */
    private $settingsExt;
    /** @var SystemFile */
    private $sut;

    protected function setUp(): void
    {
        $this->fileRepository = $this->createMock(FileRepositoryInterface::class);
        $this->getVarDirectoryPath = $this->getMockBuilder(GetVarDirectoryPath::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->io = $this->getMockBuilder(IoFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileDriver = $this->getMockBuilder(FileDriver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settings = $this->createMock(FileUploaderSettingsInterface::class);
        $this->settingsExt = $this->createMock(FileUploaderSettingsExtensionInterface::class);
        $this->settings->method('getExtensionAttributes')->willReturn($this->settingsExt);
        $this->sut = new SystemFile(
            $this->fileRepository,
            $this->getVarDirectoryPath,
            $this->io,
            $this->fileDriver
        );
    }

    public function testResolveReturnsEmptyWhenNoSystemFilePath(): void
    {
        $this->settingsExt->method('getSystemFilePath')->willReturn(null);
        $this->io->expects($this->never())->method('fileExists');
        $this->fileRepository->expects($this->never())->method('create');
        $result = $this->sut->resolve($this->settings);
        self::assertSame([], $result);
    }

    public function testResolveThrowsWhenFileDoesNotExist(): void
    {
        $filePath = '/var/import/missing.csv';
        $this->settingsExt->method('getSystemFilePath')->willReturn($filePath);
        $this->io->expects($this->once())->method('fileExists')->with($filePath)->willReturn(false);
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('File not found.');
        $this->sut->resolve($this->settings);
    }

    public function testResolveCopiesToProvidedPathAndSaves(): void
    {
        $filePath = '/var/import/file.csv';
        $baseName = 'file.csv';
        $size = 321;
        $pastePath = '/var/target/';
        $this->settingsExt->method('getSystemFilePath')->willReturn($filePath);
        $this->io->method('fileExists')->with($filePath)->willReturn(true);
        $this->io->method('getPathInfo')->with($filePath)->willReturn(['basename' => $baseName]);
        $this->fileDriver->method('stat')->with($filePath)->willReturn(['size' => $size]);
        $this->settingsExt->method('getPathToPaste')->willReturn($pastePath);
        $this->io->expects($this->once())->method('mkdir')->with($pastePath);
        $this->io->expects($this->once())
            ->method('cp')
            ->with(
                $filePath,
                $this->callback(function (string $to) use ($pastePath, $baseName, $filePath, $size): bool {
                    // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
                    $expectedPrefix = $pastePath . md5($filePath . $size) . '_' . $baseName;

                    // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
                    return str_ends_with($to, md5($filePath . $size) . '_' . $baseName)
                        && str_starts_with($to, $pastePath);
                })
            );
        $file = $this->createMock(FileInterface::class);
        $this->fileRepository->expects($this->once())->method('create')->willReturn($file);
        $file->expects($this->once())
            ->method('setName')
            ->with($this->callback(function (string $name) use ($filePath, $size, $baseName): bool {
                // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
                return $name === md5($filePath . $size) . '_' . $baseName;
            }))
            ->willReturnSelf();
        $file->expects($this->once())->method('setPath')->with($pastePath)->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($file);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file], $result);
    }

    public function testResolveUsesVarDirectoryWhenPathNotProvided(): void
    {
        $filePath = '/x/y/z.csv';
        $baseName = 'z.csv';
        $size = 999;
        $varBase = '/var/magento/var';
        $expectedDir = $varBase . DIRECTORY_SEPARATOR . 'system_files' . DIRECTORY_SEPARATOR;
        $this->settingsExt->method('getSystemFilePath')->willReturn($filePath);
        $this->io->method('fileExists')->with($filePath)->willReturn(true);
        $this->io->method('getPathInfo')->with($filePath)->willReturn(['basename' => $baseName]);
        $this->fileDriver->method('stat')->with($filePath)->willReturn(['size' => $size]);
        $this->settingsExt->method('getPathToPaste')->willReturn(null);
        $this->getVarDirectoryPath->expects($this->once())->method('execute')->willReturn($varBase);
        $this->io->expects($this->once())->method('mkdir')->with($expectedDir);
        $this->io->expects($this->once())
            ->method('cp')
            ->with(
                $filePath,
                $this->callback(function (string $to) use ($expectedDir, $filePath, $size, $baseName): bool {
                    // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
                    return $to === $expectedDir . md5($filePath . $size) . '_' . $baseName;
                })
            );
        $file = $this->createMock(FileInterface::class);
        $this->fileRepository->method('create')->willReturn($file);
        $file->expects($this->once())
            ->method('setName')
            // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
            ->with(md5($filePath . $size) . '_' . $baseName)
            ->willReturnSelf();
        $file->expects($this->once())->method('setPath')->with($expectedDir)->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($file);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file], $result);
    }
}
