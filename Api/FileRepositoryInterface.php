<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Provides CRUD functionality to the file entity
 */
interface FileRepositoryInterface
{
    /**
     * Get the file by ID
     *
     * @param int $fileId
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $fileId): Data\FileInterface;

    /**
     * Save the file
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileInterface $file
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\FileInterface $file): int;

    /**
     * Delete the file
     *
     * @param \EPuzzle\FileUploader\Api\Data\FileInterface $file
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\FileInterface $file): void;

    /**
     * Get the list of files
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResults;

    /**
     * Create the file
     *
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface
     */
    public function create(): Data\FileInterface;

    /**
     * Create search criteria
     *
     * @return \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function createSearchCriteriaBuilder(): SearchCriteriaBuilder;
}
