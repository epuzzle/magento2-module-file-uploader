<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * The search results for the file entity
 */
class FileSearchResults extends SearchResults implements FileSearchResultsInterface
{
    /**
     * @inheritDoc
     */
    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @inheritDoc
     */
    // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    public function setItems(array $items)
    {
        return parent::setItems($items);
    }
}
