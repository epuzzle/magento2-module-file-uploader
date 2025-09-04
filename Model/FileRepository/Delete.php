<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Model\File;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Used to delete the file entity
 */
class Delete
{
    /**
     * Save
     *
     * @param Resource $resource
     */
    public function __construct(
        private readonly Resource $resource
    ) {
    }

    /**
     * Delete the file entity
     *
     * @param FileInterface $file
     * @return int
     * @throws CouldNotDeleteException
     */
    public function execute(FileInterface $file): int
    {
        try {
            /** @var File $file */
            $this->resource->delete($file);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the file entity'), $exception);
        }

        return $file->getEntityId();
    }
}
