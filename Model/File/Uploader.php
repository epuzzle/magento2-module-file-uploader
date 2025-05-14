<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\File;

use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use EPuzzle\FileUploader\Model\ConfigProvider;

/**
 * Uploads fields to the file system
 */
class Uploader extends \Magento\MediaStorage\Model\File\Uploader
{
    /**
     * Uploader
     *
     * @param string $fileId
     * @param Database $coreFileStorageDb
     * @param Storage $coreFileStorage
     * @param NotProtectedExtension $validator
     * @param UrlInterface $url
     * @param ConfigProvider $configProvider
     * @param Filesystem|null $filesystem
     */
    public function __construct(
        string $fileId,
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        NotProtectedExtension $validator,
        private readonly UrlInterface $url,
        private readonly ConfigProvider $configProvider,
        Filesystem $filesystem = null
    ) {
        parent::__construct(
            $fileId,
            $coreFileStorageDb,
            $coreFileStorage,
            $validator,
            $filesystem
        );
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave($result)
    {
        $this->_result['url'] = $this->getFileUrl($result['file']);

        return parent::_afterSave($result);
    }

    /**
     * Get URL to the file
     *
     * @param string $fileName
     * @return string
     */
    private function getFileUrl(string $fileName): string
    {
        $baseUrl = $this->url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $directory = $this->configProvider->getMediaDirectory();
        $url = $baseUrl . rtrim($directory, '/') . '/' . ltrim($fileName, '/');

        return str_replace(' ', '_', $url);
    }
}
