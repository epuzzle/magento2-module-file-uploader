<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Model\File;
use EPuzzle\FileUploader\Model\ResourceModel\File as Resource;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Used to get the file entity by ID
 */
class Get
{
    /**
     * Get
     *
     * @param Resource $resource
     * @param Create $create
     */
    public function __construct(
        private readonly Resource $resource,
        private readonly Create $create
    ) {
    }

    /**
     * Get the file entity by ID
     *
     * @param int $fileId
     * @return FileInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $fileId): FileInterface
    {
        /** @var File $file */
        $file = $this->create->execute();
        $this->resource->load($file, $fileId);
        if (!$file->getEntityId()) {
            throw new NoSuchEntityException(
                __('File with id "%entityId" does not exist.', ['entityId' => $fileId])
            );
        }

        return $file;
    }
}
