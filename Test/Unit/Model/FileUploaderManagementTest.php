<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterfaceFactory;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Api\FileUploaderManagementInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement::upload
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement::getMediaDirectoryPath
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement::createFileUploaderSettings
 */
class FileUploaderManagementTest extends TestCase
{
    /**
     * @var GetMediaDirectoryPath&MockObject
     */
    private $getMediaDirectoryPath;
    /**
     * @var FileResolverInterface&MockObject
     */
    private $fileResolver;
    /**
     * @var FileUploaderSettingsInterfaceFactory&MockObject
     */
    private $settingsFactory;
    /**
     * @var FileUploaderManagementInterface
     */
    private $sut;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->getMediaDirectoryPath = $this->getMockBuilder(GetMediaDirectoryPath::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->fileResolver = $this->createMock(FileResolverInterface::class);
        $this->settingsFactory = $this->getMockBuilder(FileUploaderSettingsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sut = new FileUploaderManagement(
            $this->getMediaDirectoryPath,
            $this->fileResolver,
            $this->settingsFactory
        );
        self::assertInstanceOf(FileUploaderManagementInterface::class, $this->sut);
    }

    public function testUploadUsesProvidedSettings(): void
    {
        $settings = $this->createMock(FileUploaderSettingsInterface::class);
        $expected = [['id' => 1]];
        $this->settingsFactory->expects($this->never())
            ->method('create');
        $this->fileResolver->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($settings))
            ->willReturn($expected);
        $result = $this->sut->upload($settings);
        self::assertSame($expected, $result);
    }

    public function testUploadCreatesSettingsWhenNotProvided(): void
    {
        $settings = $this->createMock(FileUploaderSettingsInterface::class);
        $expected = [['id' => 2]];
        $this->settingsFactory->expects($this->once())
            ->method('create')
            ->willReturn($settings);
        $this->fileResolver->expects($this->once())
            ->method('resolve')
            ->with($this->identicalTo($settings))
            ->willReturn($expected);
        $result = $this->sut->upload();
        self::assertSame($expected, $result);
    }

    public function testGetMediaDirectoryPathDelegates(): void
    {
        $this->getMediaDirectoryPath->expects($this->once())
            ->method('execute')
            ->willReturn('/var/www/pub/media');
        $result = $this->sut->getMediaDirectoryPath();
        self::assertSame('/var/www/pub/media', $result);
    }

    public function testCreateFileUploaderSettingsDelegatesToFactory(): void
    {
        $settings = $this->createMock(FileUploaderSettingsInterface::class);
        $this->settingsFactory->expects($this->once())
            ->method('create')
            ->willReturn($settings);
        $result = $this->sut->createFileUploaderSettings();
        self::assertSame($settings, $result);
    }
}
