<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * The resource model for the file entity
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class File extends AbstractDb
{
    public const string TABLE_NAME = 'epuzzle_file_uploader_file';

    public const string PK = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PK);
    }
}
