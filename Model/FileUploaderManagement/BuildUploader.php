<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Model\ConfigProvider;
use EPuzzle\FileUploader\Model\File\Uploader;
use EPuzzle\FileUploader\Model\File\UploaderFactory;

/**
 * Used to build the file builder object
 */
class BuildUploader
{
    /**
     * BuildUploader
     *
     * @param UploaderFactory $uploaderFactory
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private readonly UploaderFactory $uploaderFactory,
        private readonly ConfigProvider $configProvider
    ) {
    }

    /**
     * Builds the file builder object
     *
     * @return Uploader
     */
    public function execute(): Uploader
    {
        /** @var Uploader $uploader */
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        $fieldId = $_POST['param_name'] ?? 'files[0]';
        $fieldId = $fieldId === 'undefined' ? 'files[0]' : $fieldId;
        $fieldId = $fieldId === 'files[]' ? 'files[0]' : $fieldId;
        $uploader = $this->uploaderFactory->create(['fileId' => $fieldId]);
        $uploader->setAllowedExtensions($this->configProvider->getAllowedExtensions());
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(true);

        return $uploader;
    }
}
