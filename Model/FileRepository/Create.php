<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileInterfaceFactory;

/**
 * Used to create the file entity
 */
class Create
{
    /**
     * Create
     *
     * @param FileInterfaceFactory $fileFactory
     */
    public function __construct(
        private readonly FileInterfaceFactory $fileFactory
    ) {
    }

    /**
     * Create the file entity
     *
     * @return FileInterface
     */
    public function execute(): FileInterface
    {
        return $this->fileFactory->create();
    }
}
