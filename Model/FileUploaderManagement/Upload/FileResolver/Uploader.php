<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileUploaderManagement\Upload\FileResolver;

use EPuzzle\FileUploader\Api\Data\FileUploaderSettingsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use EPuzzle\FileUploader\Api\FileResolverInterface;
use EPuzzle\FileUploader\Model\ConfigProvider;
use EPuzzle\FileUploader\Model\FileUploaderManagement\GetMediaDirectoryPath;
use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Used to resolve files from the $_FILES
 */
class Uploader implements FileResolverInterface
{
    /**
     * Uploader
     *
     * @param FileRepositoryInterface $fileRepository
     * @param GetMediaDirectoryPath $getMediaDirectoryPath
     * @param File $fileAdapter
     * @param UploaderFactory $uploaderFactory
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly GetMediaDirectoryPath $getMediaDirectoryPath,
        private readonly File $fileAdapter,
        private readonly UploaderFactory $uploaderFactory,
        private readonly ConfigProvider $configProvider
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(FileUploaderSettingsInterface $settings): array
    {
        $pathToPaste = $settings->getExtensionAttributes()->getPathToPaste();
        if (!$pathToPaste) {
            $pathToPaste = $this->getMediaDirectoryPath->execute();
            $pathToPaste .= DIRECTORY_SEPARATOR . 'uploader' . DIRECTORY_SEPARATOR;
        }
        $this->fileAdapter->mkdir($pathToPaste);
        $files = [];
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        if (!empty($_FILES)) {
            try {
                $fileData = $this->buildUploader($settings)->save($pathToPaste);
            } catch (Exception $exception) {
                throw new FileSystemException(
                    __('Could not upload the file: %error', ['error' => $exception->getMessage()]),
                    $exception
                );
            }
            $file = $this->fileRepository->create();
            $file->setType($fileData['type']);
            $file->setName($fileData['file']);
            $file->setPath($fileData['path']);
            $this->fileRepository->save($file);
            $files[] = $file;
        }

        return $files;
    }

    /**
     * Builds the uploader object
     *
     * @param FileUploaderSettingsInterface $settings
     * @return FileUploader
     */
    public function buildUploader(FileUploaderSettingsInterface $settings): FileUploader
    {
        // builds the uploader object
        /** @var FileUploader $uploader */
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        $fieldId = $_POST['param_name'] ?? 'files[0]';
        $fieldId = $fieldId === 'undefined' ? 'files[0]' : $fieldId;
        $fieldId = $fieldId === 'files[]' ? 'files[0]' : $fieldId;
        $uploader = $this->uploaderFactory->create(['fileId' => $fieldId]);
        // sets user values
        $eAttributes = $settings->getExtensionAttributes();
        $uploader->setAllowCreateFolders(
            null === $eAttributes->getUploaderAllowCreateFolders()
                ? true
                : $eAttributes->getUploaderAllowCreateFolders()
        );
        $uploader->setAllowRenameFiles(
            null === $eAttributes->getUploaderAllowRenameFiles() ? true : $eAttributes->getUploaderAllowRenameFiles()
        );
        $uploader->setAllowedExtensions(
            null === $eAttributes->getUploaderAllowedExtensions()
                ? $this->configProvider->getAllowedExtensions()
                : $eAttributes->getUploaderAllowedExtensions()
        );
        if (null !== $eAttributes->getUploaderFilenamesCaseSensitivity()) {
            $uploader->setFilenamesCaseSensitivity($eAttributes->getUploaderFilenamesCaseSensitivity());
        }
        if (null !== $eAttributes->getUploaderFilesDispersion()) {
            $uploader->setFilesDispersion($eAttributes->getUploaderFilesDispersion());
        }

        return $uploader;
    }
}
