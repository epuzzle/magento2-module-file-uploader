<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\FileUploaderSettings;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\FileUploaderSettings
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\FileUploaderSettings::getExtensionAttributes
 * @covers \EPuzzle\FileUploader\Model\FileUploaderManagement\FileUploaderSettings::setExtensionAttributes
 */
class FileUploaderSettingsTest extends TestCase
{
    /** @var FileUploaderSettingsExtensionInterface&MockObject */
    private $extensionAttributes;

    protected function setUp(): void
    {
        $this->extensionAttributes = $this->createMock(FileUploaderSettingsExtensionInterface::class);
    }

    public function testSetAndGetExtensionAttributes(): void
    {
        /** @var FileUploaderSettings&MockObject $sut */
        $sut = $this->getMockBuilder(FileUploaderSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_getExtensionAttributes', '_setExtensionAttributes'])
            ->getMock();
        $sut->expects($this->once())
            ->method('_setExtensionAttributes')
            ->with($this->extensionAttributes);
        $sut->expects($this->once())
            ->method('_getExtensionAttributes')
            ->willReturn($this->extensionAttributes);
        $sut->setExtensionAttributes($this->extensionAttributes);
        $result = $sut->getExtensionAttributes();
        self::assertSame($this->extensionAttributes, $result);
    }

    public function testSetExtensionAttributesOverridesPreviousValue(): void
    {
        $first = $this->createMock(FileUploaderSettingsExtensionInterface::class);
        $second = $this->createMock(FileUploaderSettingsExtensionInterface::class);
        /** @var FileUploaderSettings&MockObject $sut */
        $sut = $this->getMockBuilder(FileUploaderSettings::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_getExtensionAttributes', '_setExtensionAttributes'])
            ->getMock();
        $idx = 0;
        $expected = [$first, $second];
        $sut->expects($this->exactly(2))
            ->method('_setExtensionAttributes')
            ->willReturnCallback(function ($arg) use (&$idx, $expected) {
                TestCase::assertSame($expected[$idx], $arg);
                $idx++;

                return null;
            });
        $sut->expects($this->exactly(2))
            ->method('_getExtensionAttributes')
            ->willReturnOnConsecutiveCalls($first, $second);
        $sut->setExtensionAttributes($first);
        self::assertSame($first, $sut->getExtensionAttributes());
        $sut->setExtensionAttributes($second);
        self::assertSame($second, $sut->getExtensionAttributes());
    }
}
