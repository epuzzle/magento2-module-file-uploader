<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model;

use EPuzzle\FileUploader\Model\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\ConfigProvider::getAllowedExtensions
 * @covers \EPuzzle\FileUploader\Model\ConfigProvider::getMediaDirectory
 * @covers \EPuzzle\FileUploader\Model\ConfigProvider::getVarDirectory
 */
class ConfigProviderTest extends TestCase
{
    /**
     * @var ScopeConfigInterface&MockObject
     */
    private $scopeConfig;
    /**
     * @var ConfigProvider
     */
    private $sut;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->sut = new ConfigProvider($this->scopeConfig);
    }

    /**
     * @dataProvider allowedExtensionsProvider
     */
    public function testGetAllowedExtensionsReturnsFilteredArray(
        ?int $websiteId,
        string $configValue,
        array $expected
    ): void {
        $path = 'epuzzle_file_uploader/settings/allowed_extensions';
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with($path, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($configValue);
        $result = $this->sut->getAllowedExtensions($websiteId);
        self::assertSame(array_values($expected), array_values($result));
    }

    public function allowedExtensionsProvider(): array
    {
        return [
            'typical list, no website id' => [
                'websiteId' => null,
                'configValue' => 'jpg,png,gif',
                'expected' => ['jpg', 'png', 'gif']
            ],
            'with spaces preserved (trim only for filter)' => [
                'websiteId' => 2,
                'configValue' => ' jpg , png ',
                'expected' => [' jpg ', ' png ']
            ],
            'empty string -> empty array' => [
                'websiteId' => 1,
                'configValue' => '',
                'expected' => []
            ],
            'double commas, space and "0" filtered' => [
                'websiteId' => 3,
                'configValue' => 'pdf,, ,csv,0',
                'expected' => ['pdf', 'csv']
            ],
        ];
    }

    public function testGetMediaDirectoryCastsToString(): void
    {
        $path = 'epuzzle_file_uploader/settings/media_directory';
        $this->scopeConfig->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive([$path], [$path], [$path])
            ->willReturnOnConsecutiveCalls('media', null, 123);
        self::assertSame('media', $this->sut->getMediaDirectory());
        self::assertSame('', $this->sut->getMediaDirectory());
        self::assertSame('123', $this->sut->getMediaDirectory());
    }

    public function testGetVarDirectoryCastsToString(): void
    {
        $path = 'epuzzle_file_uploader/settings/var_directory';
        $this->scopeConfig->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive([$path], [$path], [$path])
            ->willReturnOnConsecutiveCalls('var', null, 456);
        self::assertSame('var', $this->sut->getVarDirectory());
        self::assertSame('', $this->sut->getVarDirectory());
        self::assertSame('456', $this->sut->getVarDirectory());
    }
}
