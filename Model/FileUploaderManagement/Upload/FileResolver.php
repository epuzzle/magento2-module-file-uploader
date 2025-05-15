<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload;

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
    public function resolve(): array
    {
        $files = [];
        foreach ($this->resolvers as $resolver) {
            array_push($files, ...$resolver->resolve());
        }

        return $files;
    }
}
