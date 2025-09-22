<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\ExternalLink;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem\Io\File as IoFile;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\ExternalLink
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver\ExternalLink::resolve
 */
class ExternalLinkTest extends TestCase
{
    /** @var HttpRequest&MockObject */
    private $request;
    /** @var FileRepositoryInterface&MockObject */
    private $fileRepository;
    /** @var GetMediaDirectoryPath&MockObject */
    private $getMediaDirectoryPath;
    /** @var IoFile&MockObject */
    private $io;
    /** @var FileUploaderSettingsInterface&MockObject */
    private $settings;
    /** @var FileUploaderSettingsExtensionInterface&MockObject */
    private $settingsExt;
    /** @var ExternalLink */
    private $sut;

    protected function setUp(): void
    {
        $this->request = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getParam', 'getContent'])
            ->getMock();
        $this->fileRepository = $this->getMockBuilder(FileRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->getMediaDirectoryPath = $this->getMockBuilder(GetMediaDirectoryPath::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->io = $this->getMockBuilder(IoFile::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settings = $this->getMockBuilder(FileUploaderSettingsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settingsExt = $this->getMockBuilder(FileUploaderSettingsExtensionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->settings->method('getExtensionAttributes')->willReturn($this->settingsExt);
        $this->sut = new ExternalLink(
            $this->request,
            $this->fileRepository,
            $this->getMediaDirectoryPath,
            $this->io
        );
    }

    public function testResolveReturnsEmptyWhenNoLinks(): void
    {
        $this->request->method('getParam')->with('external_links')->willReturn(null);
        $this->request->method('getContent')->willReturn('');
        $this->io->expects($this->never())->method('mkdir');
        $this->fileRepository->expects($this->never())->method('create');
        $result = $this->sut->resolve($this->settings);
        self::assertSame([], $result);
    }

    public function testResolveDownloadsAndSavesWithProvidedPath(): void
    {
        $links = ['https://example.com/a/test1.csv', 'https://example.com/b/test2.csv'];
        $pathToPaste = '/var/media/custom/';
        $this->request->method('getParam')->with('external_links')->willReturn($links);
        $this->request->method('getContent')->willReturn('');
        $this->settingsExt->method('getPathToPaste')->willReturn($pathToPaste);
        $this->io->expects($this->once())->method('mkdir')->with($pathToPaste);
        $this->io->method('getPathInfo')->willReturnCallback(
            function (string $url): array {
                return ['basename' => basename(parse_url($url, PHP_URL_PATH) ?? '')];
            }
        );
        $this->io->expects($this->exactly(2))
            ->method('fileExists')
            ->with($this->callback(fn ($p) => str_starts_with($p, $pathToPaste)))
            ->willReturn(false);
        $this->io->expects($this->exactly(2))->method('read')->willReturn(true);
        $file1 = $this->createMock(FileInterface::class);
        $file2 = $this->createMock(FileInterface::class);
        $this->fileRepository->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($file1, $file2);
        foreach ([$file1, $file2] as $file) {
            $file->expects($this->once())->method('setName')->with($this->isType('string'))
                ->willReturnSelf();
            $file->expects($this->once())->method('setPath')->with($pathToPaste)
                ->willReturnSelf();
        }
        $this->fileRepository->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive([$file1], [$file2]);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file1, $file2], $result);
    }

    public function testResolveUsesMediaDirWhenNoPathProvided(): void
    {
        $links = ['https://host.tld/f.csv'];
        $mediaBase = '/var/www/pub/media';
        $expectedPath = $mediaBase . DIRECTORY_SEPARATOR . 'external_links' . DIRECTORY_SEPARATOR;
        $this->request->method('getParam')->with('external_links')->willReturn($links);
        $this->request->method('getContent')->willReturn('');
        $this->settingsExt->method('getPathToPaste')->willReturn(null);
        $this->getMediaDirectoryPath->expects($this->once())->method('execute')->willReturn($mediaBase);
        $this->io->expects($this->once())->method('mkdir')->with($expectedPath);
        $this->io->method('getPathInfo')->willReturn(['basename' => 'f.csv']);
        $this->io->method('fileExists')->willReturn(false);
        $this->io->method('read')->willReturn(true);
        $file = $this->createMock(FileInterface::class);
        $this->fileRepository->method('create')->willReturn($file);
        $file->expects($this->once())->method('setName')->with($this->isType('string'))
            ->willReturnSelf();
        $file->expects($this->once())->method('setPath')->with($expectedPath)
            ->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($file);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file], $result);
    }

    public function testResolveSkipsDownloadIfFileExists(): void
    {
        $links = ['https://cdn/x.csv'];
        $this->request->method('getParam')->with('external_links')->willReturn($links);
        $this->request->method('getContent')->willReturn('');
        $this->settingsExt->method('getPathToPaste')->willReturn('/p/');
        $this->io->expects($this->once())->method('mkdir')->with('/p/');
        $this->io->method('getPathInfo')->willReturn(['basename' => 'x.csv']);
        $this->io->method('fileExists')->willReturn(true);
        $this->io->expects($this->never())->method('read');
        $file = $this->createMock(FileInterface::class);
        $this->fileRepository->method('create')->willReturn($file);
        $file->expects($this->once())->method('setName')->with($this->isType('string'))
            ->willReturnSelf();
        $file->expects($this->once())->method('setPath')->with('/p/')
            ->willReturnSelf();
        $this->fileRepository->expects($this->once())->method('save')->with($file);
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file], $result);
    }

    public function testResolveMergesLinksFromBodyJson(): void
    {
        $paramLinks = ['https://a/t1.csv'];
        $bodyLinks = ['https://b/t2.csv'];
        $this->request->method('getParam')->with('external_links')->willReturn($paramLinks);
        $this->request->method('getContent')
            ->willReturn(json_encode(['external_links' => $bodyLinks], JSON_THROW_ON_ERROR));
        $this->settingsExt->method('getPathToPaste')->willReturn('/paste/');
        $this->io->expects($this->once())->method('mkdir')->with('/paste/');
        $this->io->method('getPathInfo')->willReturn(['basename' => 'x.csv']);
        $this->io->method('fileExists')->willReturn(false);
        $this->io->method('read')->willReturn(true);
        $file1 = $this->createMock(FileInterface::class);
        $file2 = $this->createMock(FileInterface::class);
        $this->fileRepository->method('create')->willReturnOnConsecutiveCalls($file1, $file2);
        $file1->method('setName')->willReturnSelf();
        $file1->method('setPath')->willReturnSelf();
        $file2->method('setName')->willReturnSelf();
        $file2->method('setPath')->willReturnSelf();
        $this->fileRepository->expects($this->exactly(2))->method('save');
        $result = $this->sut->resolve($this->settings);
        self::assertSame([$file1, $file2], $result);
    }

    public function testResolveThrowsWhenReadFails(): void
    {
        $links = ['https://host/f.csv'];
        $this->request->method('getParam')->with('external_links')->willReturn($links);
        $this->request->method('getContent')->willReturn('');
        $this->settingsExt->method('getPathToPaste')->willReturn('/p/');
        $this->io->method('mkdir')->with('/p/');
        $this->io->method('getPathInfo')->willReturn(['basename' => 'f.csv']);
        $this->io->method('fileExists')->willReturn(false);
        $this->io->method('read')->willReturn(false);
        $this->fileRepository->method('create')->willReturn($this->createMock(FileInterface::class));
        $this->expectException(NotFoundException::class);
        $this->sut->resolve($this->settings);
    }
}
