<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * The file entity model
 */
class File extends AbstractModel implements FileInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\File::class);
        $this->setIdFieldName(ResourceModel\File::PK);
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        if (!$this->getIdentifier()) {
            $this->setData('identifier', sha1(uniqid((string)time(), true)));
        }

        return parent::beforeSave();
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return (int)parent::getEntityId();
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId): FileInterface
    {
        return parent::setEntityId((int)$entityId);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return (string)$this->getData('identifier');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->getData('type');
    }

    /**
     * @inheritDoc
     */
    public function setType(string $value): void
    {
        $this->setData('type', $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->getData('name');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $value): void
    {
        $this->setData('name', $value);
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return (string)$this->getData('path');
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $value): void
    {
        $this->setData('path', $value);
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return (int)$this->getData('size');
    }

    /**
     * @inheritDoc
     */
    public function setSize(int $value): void
    {
        $this->setData('size', $value);
    }

    /**
     * @inheritDoc
     */
    public function getMediaUrl(): string
    {
        return (string)$this->getData('media_url');
    }

    /**
     * @inheritDoc
     */
    public function setMediaUrl(string $value): void
    {
        $this->setData('media_url', $value);
    }
}
