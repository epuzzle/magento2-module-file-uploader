<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\ResourceModel\File;

use EPuzzle\FileUploader\Model\File;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * The collection model for the file entity
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Resource::PK;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(File::class, Resource::class);
    }
}
