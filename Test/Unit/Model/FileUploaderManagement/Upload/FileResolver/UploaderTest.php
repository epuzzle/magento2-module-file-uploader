<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Model\ConfigProvider;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\Uploader;
use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\Uploader
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\Uploader::resolve
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\Uploader::buildUploader
 */
class UploaderTest extends TestCase
{
    /** @var FileRepositoryInterface&MockObject */
    private $fileRepository;
    /** @var GetMediaDirectoryPath&MockObject */
    private $getMediaDirectoryPath;
    /** @var File&MockObject */
    private $fileAdapter;
    /** @var UploaderFactory&MockObject */
    private $uploaderFactory;
    /** @var ConfigProvider&MockObject */
    private $configProvider;
    /** @var FileUploaderSettingsInterface&MockObject */
    private $settings;
    /** @var FileUploaderSettingsExtensionInterface&MockObject */
    private $settingsExt;
    /** @var Uploader */
    private $sut;
    /** @var array */
    private $backupFiles;
    /** @var array */
    private $backupPost;

    protected function setUp(): void
    {
        $this->backupFiles = $_FILES ?? [];
        $this->backupPost = $_POST ?? [];
        $this->fileRepository = $this->createMock(FileRepositoryInterface::class);
        $this->getMediaDirectoryPath = $this->createMock(GetMediaDirectoryPath::class);
        $this->fileAdapter = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->uploaderFactory = $this->getMockBuilder(UploaderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->settings = $this->createMock(FileUploaderSettingsInterface::class);
        $this->settingsExt = $this->createMock(FileUploaderSettingsExtensionInterface::class);
        $this->settings->method('getExtensionAttributes')->willReturn($this->settingsExt);
        $this->sut = new Uploader(
            $this->fileRepository,
            $this->getMediaDirectoryPath,
            $this->fileAdapter,
            $this->uploaderFactory,
            $this->configProvider
        );
        self::assertInstanceOf(FileResolverInterface::class, $this->sut);
    }

    protected function tearDown(): void
    {
        $_FILES = $this->backupFiles;
        $_POST = $this->backupPost;
    }

    public function testResolveWithProvidedPathAndFilesUploadsAndSaves(): void
    {
        $_FILES = ['file' => ['name' => 'x']];
        $path = '/custom/path/';
        $this->settingsExt->method('getPathToPaste')->willReturn($path);
        $this->fileAdapter->expects($this->once())
            ->method('mkdir')
            ->with($path, $this->anything(), $this->anything());
        $uploader = $this->createMock(FileUploader::class);
        $this->prepareUploaderDefaults($uploader);
        $this->uploaderFactory->expects($this->once())
            ->method('create')
            ->with(['fileId' => 'files[0]'])
            ->willReturn($uploader);
        $fileData = ['type' => 'image/png', 'file' => 'photo.png', 'path' => $path];
        $uploader->expects($this->once())->method('save')->with($path)->willReturn($fileData);
        $fileEntity = $this->createMock(\EPuzzle\FileUploader\Api\Data\FileInterface::class);
        $this->fileRepository->expects($this->once())->method('create')->willReturn($fileEntity);
        $fileEntity->expects($this->once())->method('setType')->with('image/png')->willReturnSelf();
        $fileEntity->expects($this->once())->method('setName')->with('photo.png')->willReturnSelf();
        $fileEntity->expects($this->once())->method('setPath')->with($path)->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($fileEntity);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$fileEntity], $result);
    }

    public function testResolveWithoutProvidedPathUsesMediaDirectory(): void
    {
        $_FILES = ['file' => ['name' => 'x']];
        $this->settingsExt->method('getPathToPaste')->willReturn('');
        $baseMedia = '/var/www/pub/media';
        $this->getMediaDirectoryPath->expects($this->once())
            ->method('execute')
            ->willReturn($baseMedia);
        $expectedPath = rtrim($baseMedia, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'uploader' . DIRECTORY_SEPARATOR;
        $this->fileAdapter->expects($this->once())
            ->method('mkdir')
            ->with($expectedPath, $this->anything(), $this->anything());
        $uploader = $this->createMock(FileUploader::class);
        $this->prepareUploaderDefaults($uploader);
        $this->uploaderFactory->method('create')->willReturn($uploader);
        $fileData = ['type' => 'text/plain', 'file' => 'doc.txt', 'path' => $expectedPath];
        $uploader->method('save')->with($expectedPath)->willReturn($fileData);
        $fileEntity = $this->createMock(\EPuzzle\FileUploader\Api\Data\FileInterface::class);
        $this->fileRepository->method('create')->willReturn($fileEntity);
        $fileEntity->expects($this->once())->method('setType')->with('text/plain')->willReturnSelf();
        $fileEntity->expects($this->once())->method('setName')->with('doc.txt')->willReturnSelf();
        $fileEntity->expects($this->once())->method('setPath')->with($expectedPath)->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($fileEntity);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$fileEntity], $result);
    }

    public function testResolveReturnsEmptyArrayWhenNoFiles(): void
    {
        $_FILES = [];
        $this->settingsExt->method('getPathToPaste')->willReturn('');
        $baseMedia = '/var/www/pub/media';
        $this->getMediaDirectoryPath->method('execute')->willReturn($baseMedia);
        $expectedPath = rtrim($baseMedia, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'uploader' . DIRECTORY_SEPARATOR;
        $this->fileAdapter->expects($this->once())
            ->method('mkdir')
            ->with($expectedPath, $this->anything(), $this->anything());
        $this->uploaderFactory->expects($this->never())->method('create');
        $this->fileRepository->expects($this->never())->method('create');
        $this->fileRepository->expects($this->never())->method('save');
        $result = $this->sut->resolve($this->settings);
        self::assertSame([], $result);
    }

    public function testResolveWrapsExceptionFromSaveIntoFileSystemException(): void
    {
        $_FILES = ['file' => ['name' => 'x']];
        $path = '/path/';
        $this->settingsExt->method('getPathToPaste')->willReturn($path);
        $this->fileAdapter->expects($this->once())
            ->method('mkdir')
            ->with($path, $this->anything(), $this->anything());
        $uploader = $this->createMock(FileUploader::class);
        $this->prepareUploaderDefaults($uploader);
        $this->uploaderFactory->method('create')->willReturn($uploader);
        $uploader->expects($this->once())
            ->method('save')
            ->with($path)
            ->willThrowException(new Exception('boom'));
        $this->fileRepository->expects($this->never())->method('create');
        $this->fileRepository->expects($this->never())->method('save');
        $this->expectException(FileSystemException::class);
        $this->expectExceptionMessage('Could not upload the file:');
        $this->sut->resolve($this->settings);
    }

    /**
     * @dataProvider dataProviderForBuildUploaderFieldId
     */
    public function testBuildUploaderDetectsFieldIdFromPost(?string $paramName, string $expectedId): void
    {
        $_POST = [];
        if (null !== $paramName) {
            $_POST['param_name'] = $paramName;
        }
        $uploader = $this->createMock(FileUploader::class);
        $this->uploaderFactory->expects($this->once())
            ->method('create')
            ->with(['fileId' => $expectedId])
            ->willReturn($uploader);
        $this->settingsExt->method('getUploaderAllowCreateFolders')->willReturn(null);
        $this->settingsExt->method('getUploaderAllowRenameFiles')->willReturn(null);
        $this->settingsExt->method('getUploaderAllowedExtensions')->willReturn(null);
        $this->settingsExt->method('getUploaderFilenamesCaseSensitivity')->willReturn(null);
        $this->settingsExt->method('getUploaderFilesDispersion')->willReturn(null);
        $this->configProvider->method('getAllowedExtensions')->willReturn(['png', 'jpg']);
        $uploader->expects($this->once())->method('setAllowCreateFolders')->with(true);
        $uploader->expects($this->once())->method('setAllowRenameFiles')->with(true);
        $uploader->expects($this->once())->method('setAllowedExtensions')->with(['png', 'jpg']);
        $uploader->expects($this->never())->method('setFilenamesCaseSensitivity');
        $uploader->expects($this->never())->method('setFilesDispersion');
        $result = $this->sut->buildUploader($this->settings);
        self::assertSame($uploader, $result);
    }

    public function dataProviderForBuildUploaderFieldId(): array
    {
        return [
            'no param_name' => [null, 'files[0]'],
            'explicit undefined' => ['undefined', 'files[0]'],
            'array files[]' => ['files[]', 'files[0]'],
            'custom name' => ['my_file', 'my_file'],
        ];
    }

    public function testBuildUploaderAppliesExplicitExtensionAttributes(): void
    {
        $_POST = [];
        $uploader = $this->createMock(FileUploader::class);
        $this->uploaderFactory->method('create')->with(['fileId' => 'files[0]'])->willReturn($uploader);
        $this->settingsExt->method('getUploaderAllowCreateFolders')->willReturn(false);
        $this->settingsExt->method('getUploaderAllowRenameFiles')->willReturn(false);
        $this->settingsExt->method('getUploaderAllowedExtensions')->willReturn(['csv']);
        $this->settingsExt->method('getUploaderFilenamesCaseSensitivity')->willReturn(true);
        $this->settingsExt->method('getUploaderFilesDispersion')->willReturn(true);
        $uploader->expects($this->once())->method('setAllowCreateFolders')->with(false);
        $uploader->expects($this->once())->method('setAllowRenameFiles')->with(false);
        $uploader->expects($this->once())->method('setAllowedExtensions')->with(['csv']);
        $uploader->expects($this->once())->method('setFilenamesCaseSensitivity')->with(true);
        $uploader->expects($this->once())->method('setFilesDispersion')->with(true);
        $result = $this->sut->buildUploader($this->settings);
        self::assertSame($uploader, $result);
    }

    /**
     * Prepare uploader with default setter expectations used by resolve() tests.
     *
     * @param FileUploader&MockObject $uploader
     */
    private function prepareUploaderDefaults(MockObject $uploader): void
    {
        $this->settingsExt->method('getUploaderAllowCreateFolders')->willReturn(null);
        $this->settingsExt->method('getUploaderAllowRenameFiles')->willReturn(null);
        $this->settingsExt->method('getUploaderAllowedExtensions')->willReturn(null);
        $this->settingsExt->method('getUploaderFilenamesCaseSensitivity')->willReturn(null);
        $this->settingsExt->method('getUploaderFilesDispersion')->willReturn(null);
        $this->configProvider->method('getAllowedExtensions')->willReturn(['png']);
        $uploader->expects($this->once())->method('setAllowCreateFolders')->with(true);
        $uploader->expects($this->once())->method('setAllowRenameFiles')->with(true);
        $uploader->expects($this->once())->method('setAllowedExtensions')->with(['png']);
        $uploader->expects($this->never())->method('setFilenamesCaseSensitivity');
        $uploader->expects($this->never())->method('setFilesDispersion');
    }
}
