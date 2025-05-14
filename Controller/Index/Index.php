<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Controller\Index;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use EPuzzle\FileUploader\Api\FileUploaderManagementInterface;

/**
 * Used to upload the list of files to the file system of Magento 2
 */
class Index implements HttpPostActionInterface
{
    /**
     * Index
     *
     * @param JsonFactory $resultJsonFactory
     * @param FileUploaderManagementInterface $fileUploaderManagement
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        private readonly JsonFactory $resultJsonFactory,
        private readonly FileUploaderManagementInterface $fileUploaderManagement
    ) {
    }

    /**
     * Upload the file from the FE area
     *
     * @return Json
     */
    public function execute(): Json
    {
        try {
            $response = array_merge(
                $this->fileUploaderManagement->upload(),
                [
                    'result' => [
                        'success' => true,
                        'message' => __('File has been successfully uploaded.'),
                    ],
                ]
            );
        } catch (Exception $exception) {
            $response = [
                'error' => $exception->getMessage(),
                'result' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }

        return $this->resultJsonFactory->create()->setData($response);
    }
}
