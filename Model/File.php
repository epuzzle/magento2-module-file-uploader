<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb as AbstractDbCollection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

/**
 * The file entity model
 */
class File extends AbstractModel implements FileInterface
{
    /**
     * File
     *
     * @param File\Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDbCollection|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function __construct(
        private File\Context $context,
        Registry $registry,
        ?AbstractResource $resource = null,
        ?AbstractDbCollection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\File::class);
        $this->setIdFieldName(ResourceModel\File::PK);
    }

    /**
     * Processing object before save data
     *
     * @return File
     * @throws FileSystemException
     */
    public function beforeSave()
    {
        if (!$this->getIdentifier()) {
            $this->setData('identifier', sha1(uniqid((string)time(), true)));
        }
        if (!$this->getRelativePath()) {
            $rootDirectory = $this->context->getDirectoryList()->getRoot();
            $fileRelativePath = $this->context->getFileDriver()->getRealPath($this->getPath());
            $fileRelativePath = str_replace($rootDirectory, '', $fileRelativePath);
            $fileRelativePath = trim($fileRelativePath, DIRECTORY_SEPARATOR);
            $fileRelativePath .= DIRECTORY_SEPARATOR;
            $this->setData('relative_path', $fileRelativePath);
        }
        $filePath = $this->getFullPath();
        if (!$this->getType()) {
            $this->setType(mime_content_type($filePath));
        }
        if (!$this->getSize()) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $this->setSize(filesize($filePath));
        }
        if (!$this->getExtension()) {
            $info = $this->context->getIoFile()->getPathInfo($filePath);
            if (!empty($info['extension'])) {
                $this->setExtension(strtolower($info['extension']));
            }
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
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function setIdentifier(string $value): void
    {
        // empty implementation because the media URL is a dynamic property and is needed only for runtime scripts
        // this method needs to comply with all Magento 2 requirements regarding service contracts and the rest API
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
    public function getRelativePath(): string
    {
        return (string)$this->getData('relative_path');
    }

    /**
     * @inheritDoc
     */
    public function setRelativePath(string $value): void
    {
        $this->setData('relative_path', $value);
    }

    /**
     * @inheritDoc
     */
    public function getFullPath(): string
    {
        $root = $this->context->getDirectoryList()->getRoot();
        $fullPath = $this->context->getFileDriver()->getRealPath(
            $root . DIRECTORY_SEPARATOR . $this->getRelativePath() . DIRECTORY_SEPARATOR . $this->getName()
        );
        if (!$fullPath) {
            throw new FileSystemException(__('The file is not found.'));
        }

        return $fullPath;
    }

    /**
     * @inheritDoc
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function setFullPath(string $value): void
    {
        // empty implementation because the media URL is a dynamic property and is needed only for runtime scripts
        // this method needs to comply with all Magento 2 requirements regarding service contracts and the rest API
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
    public function getExtension(): ?string
    {
        return $this->getData('extension');
    }

    /**
     * @inheritDoc
     */
    public function setExtension(?string $value): void
    {
        $this->setData('extension', $value);
    }

    /**
     * @inheritDoc
     */
    public function getMediaUrl(): ?string
    {
        try {
            $mediaDirectory = $this->context->getDirectoryList()->getPath(DirectoryList::MEDIA);
            $mediaDirectory = rtrim($mediaDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $relativeMediaPath = str_replace($mediaDirectory, '', $this->getFullPath());
            /** @var Store $store */
            $store = $this->context->getStoreManager()->getStore();

            return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $relativeMediaPath;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function setMediaUrl(?string $value): void
    {
        // empty implementation because the media URL is a dynamic property and is needed only for runtime scripts
        // this method needs to comply with all Magento 2 requirements regarding service contracts and the rest API
    }
}
