<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsExtensionInterface;
use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * The file uploader settings entity
 */
class FileUploaderSettings extends AbstractExtensibleModel implements FileUploaderSettingsInterface
{
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): FileUploaderSettingsExtensionInterface
    {
        return $this->_getExtensionAttributes(); // @phpstan-ignore-line
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(FileUploaderSettingsExtensionInterface $value): void
    {
        $this->_setExtensionAttributes($value);
    }
}
