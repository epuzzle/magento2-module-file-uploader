<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;

/**
 * Used to resolve files for the upload process
 */
class FileResolver implements FileResolverInterface
{
    /**
     * FileResolver
     *
     * @param FileResolverInterface[] $resolvers
     */
    public function __construct(
        private array $resolvers = []
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(FileUploaderSettingsInterface $settings): array
    {
        $files = [];
        foreach ($this->resolvers as $resolver) {
            array_push($files, ...$resolver->resolve($settings));
        }

        return $files;
    }
}
