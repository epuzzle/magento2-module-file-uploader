<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api;

/**
 * Used to resolve files for the upload process
 */
interface FileResolverInterface
{
    /**
     * Resolves files for the upload process
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface $settings
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve(Data\FileUploaderSettingsInterface $settings): array;
}
