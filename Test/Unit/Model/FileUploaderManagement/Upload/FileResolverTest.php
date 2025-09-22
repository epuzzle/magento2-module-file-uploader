<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement\Upload;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver::resolve
 */
class FileResolverTest extends TestCase
{
    /** @var FileUploaderSettingsInterface&MockObject */
    private $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(FileUploaderSettingsInterface::class);
    }

    public function testResolveWithSingleResolverReturnsFiles(): void
    {
        $resolver = $this->createMock(FileResolverInterface::class);
        $files = ['file1'];
        $resolver->expects($this->once())
            ->method('resolve')
            ->with($this->settings)
            ->willReturn($files);
        $sut = new FileResolver([$resolver]);
        $result = $sut->resolve($this->settings);
        self::assertSame($files, $result);
    }

    public function testResolveWithMultipleResolversMergesResults(): void
    {
        $resolver1 = $this->createMock(FileResolverInterface::class);
        $resolver2 = $this->createMock(FileResolverInterface::class);
        $resolver1->expects($this->once())
            ->method('resolve')
            ->with($this->settings)
            ->willReturn(['file1', 'file2']);
        $resolver2->expects($this->once())
            ->method('resolve')
            ->with($this->settings)
            ->willReturn(['file3']);
        $sut = new FileResolver([$resolver1, $resolver2]);
        $result = $sut->resolve($this->settings);
        self::assertSame(['file1', 'file2', 'file3'], $result);
    }

    public function testResolveWithNoResolversReturnsEmptyArray(): void
    {
        $sut = new FileResolver([]);
        $result = $sut->resolve($this->settings);
        self::assertSame([], $result);
    }

    public function testResolvePropagatesExceptionFromResolver(): void
    {
        $resolver = $this->createMock(FileResolverInterface::class);
        $resolver->expects($this->once())
            ->method('resolve')
            ->willThrowException(new RuntimeException('fail'));
        $sut = new FileResolver([$resolver]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('fail');
        $sut->resolve($this->settings);
    }
}
