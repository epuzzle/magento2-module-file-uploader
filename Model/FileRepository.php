<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model;

use EPuzzle\FileUploader\Api\Data\FileInterface;
use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterface;
use EPuzzle\FileUploader\Api\FileRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Provides CRUD functionality to the file entity
 */
class FileRepository implements FileRepositoryInterface
{
    /**
     * FileRepository
     *
     * @param FileRepository\Get $get
     * @param FileRepository\Save $save
     * @param FileRepository\Delete $delete
     * @param FileRepository\GetList $getList
     * @param FileRepository\Create $create
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        private FileRepository\Get $get,
        private FileRepository\Save $save,
        private FileRepository\Delete $delete,
        private FileRepository\GetList $getList,
        private FileRepository\Create $create,
        private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(int $fileId): FileInterface
    {
        return $this->get->execute($fileId);
    }

    /**
     * @inheritDoc
     */
    public function save(FileInterface $file): int
    {
        return $this->save->execute($file);
    }

    /**
     * @inheritDoc
     */
    public function delete(FileInterface $file): void
    {
        $this->delete->execute($file);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $fileId): void
    {
        $this->delete($this->get($fileId));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): FileSearchResultsInterface
    {
        return $this->getList->execute($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function create(): FileInterface
    {
        return $this->create->execute();
    }

    /**
     * @inheritDoc
     */
    public function createSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilderFactory->create();
    }
}
