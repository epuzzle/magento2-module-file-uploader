<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetVarDirectoryPath;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File;

/**
 * Used to resolve files from the system files storage
 */
class SystemFile implements FileResolverInterface
{
    /**
     * SystemFile
     *
     * @param FileRepositoryInterface $fileRepository
     * @param GetVarDirectoryPath $getVarDirectoryPath
     * @param File $fileAdapter
     */
    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly GetVarDirectoryPath $getVarDirectoryPath,
        private readonly File $fileAdapter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(FileUploaderSettingsInterface $settings): array
    {
        $filePath = $settings->getExtensionAttributes()->getSystemFilePath();
        if (!$filePath) {
            // exit: the file is not provided
            return [];
        }
        if (!$this->fileAdapter->fileExists($filePath)) {
            throw new FileSystemException(__('File not found.'));
        }
        $fileName = $this->fileAdapter->getPathInfo($filePath)['basename'];
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $fileSize = filesize($filePath);
        $pathToPaste = $this->getVarDirectoryPath->execute();
        $pathToPaste .= DIRECTORY_SEPARATOR . 'system_files' . DIRECTORY_SEPARATOR;
        $this->fileAdapter->mkdir($pathToPaste);
        // The MD5 here is not for crypto cases
        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        $fileName = md5($filePath . $fileSize) . '_' . $fileName;
        $this->fileAdapter->cp($filePath, $pathToPaste . $fileName);
        $file = $this->fileRepository->create();
        $file->setName($fileName);
        $file->setPath($pathToPaste);
        $this->fileRepository->save($file);

        return [$file];
    }
}
