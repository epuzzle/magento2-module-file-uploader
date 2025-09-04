<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Model\ResourceModel\File;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Used to save the file entity
 */
class Save
{
    /**
     * Save
     *
     * @param File $resource
     */
    public function __construct(
        private readonly File $resource
    ) {
    }

    /**
     * Save the file entity
     *
     * @param FileInterface $file
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(FileInterface $file): int
    {
        try {
            $this->resource->save($file);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the file entity'), $exception);
        }

        return $file->getEntityId();
    }
}
