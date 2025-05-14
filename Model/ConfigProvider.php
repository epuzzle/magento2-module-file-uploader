<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Used to get the stores configurations
 */
class ConfigProvider
{
    /**
     * ConfigProvider
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Get the list of allowed extensions
     *
     * @param int|null $websiteId
     * @return array
     */
    public function getAllowedExtensions(int $websiteId = null): array
    {
        $value = (string)$this->scopeConfig->getValue(
            'epuzzle_file_uploader/settings/allowed_extensions',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        $value = explode(',', $value);

        return array_filter($value, static fn(string $sku) => !empty($sku));
    }

    /**
     * Get media directory name
     *
     * @return string
     */
    public function getMediaDirectory(): string
    {
        return (string)$this->scopeConfig->getValue('epuzzle_file_uploader/settings/media_directory');
    }
}
