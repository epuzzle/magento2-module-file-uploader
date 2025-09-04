<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * The file uploader settings entity
 */
interface FileUploaderSettingsInterface extends ExtensibleDataInterface
{
    /**
     * Get extension attributes object
     *
     * @return \EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface
     */
    public function getExtensionAttributes(): FileUploaderSettingsExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface $value
     * @return void
     */
    public function setExtensionAttributes(FileUploaderSettingsExtensionInterface $value): void;
}
