<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * The search results for the file entity
 */
interface FileSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get the list of files
     *
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface[]
     */
    public function getItems();

    /**
     * Set the list of files
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
