<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Used to resolve files for the upload process
 */
interface FileResolverInterface
{
    /**
     * Resolves files for the upload process
     *
     * @return FileInterface[]
     * @throws LocalizedException
     */
    public function resolve(): array;
}
