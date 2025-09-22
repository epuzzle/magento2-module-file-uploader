<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Test\Unit\Controller\Index;

use EPuzzle\FileUploader\Api\FileUploaderManagementInterface;
use EPuzzle\FileUploader\Controller\Index\Index;
use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EPuzzle\FileUploader\Controller\Index\Index
 */
class IndexTest extends TestCase
{
    /** @var JsonFactory&MockObject */
    private $resultJsonFactory;
    /** @var FileUploaderManagementInterface&MockObject */
    private $fileUploaderManagement;
    /** @var Json&MockObject */
    private $jsonResult;
    /** @var Index */
    private $sut;

    protected function setUp(): void
    {
        $this->resultJsonFactory = $this->createMock(JsonFactory::class);
        $this->fileUploaderManagement = $this->createMock(FileUploaderManagementInterface::class);
        $this->jsonResult = $this->createMock(Json::class);
        $this->resultJsonFactory->method('create')->willReturn($this->jsonResult);
        $this->sut = new Index(
            $this->resultJsonFactory,
            $this->fileUploaderManagement
        );
        self::assertInstanceOf(HttpPostActionInterface::class, $this->sut);
    }

    public function testExecuteSuccess(): void
    {
        $uploadedData = ['file' => 'test.csv'];
        $expected = array_merge(
            $uploadedData,
            [
                'result' => [
                    'success' => true,
                    'message' => __('File has been successfully uploaded.'),
                ],
            ]
        );
        $this->fileUploaderManagement->expects($this->once())
            ->method('upload')
            ->willReturn($uploadedData);
        $this->jsonResult->expects($this->once())
            ->method('setData')
            ->with($expected)
            ->willReturnSelf();
        $result = $this->sut->execute();
        self::assertSame($this->jsonResult, $result);
    }

    public function testExecuteFailure(): void
    {
        $exception = new Exception('Upload failed');
        $expected = [
            'error' => 'Upload failed',
            'result' => [
                'success' => false,
                'message' => 'Upload failed',
            ],
        ];
        $this->fileUploaderManagement->expects($this->once())
            ->method('upload')
            ->willThrowException($exception);
        $this->jsonResult->expects($this->once())
            ->method('setData')
            ->with($expected)
            ->willReturnSelf();
        $result = $this->sut->execute();
        self::assertSame($this->jsonResult, $result);
    }
}
