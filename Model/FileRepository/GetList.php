<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Model\FileRepository;

use EPuzzle\FileUploader\Api\Data\FileSearchResultsInterfaceFactory;
use EPuzzle\FileUploader\Model\ResourceModel\File\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Used to get the list of files
 */
class GetList
{
    /**
     * GetList
     *
     * @param CollectionFactory $collectionFactory
     * @param FileSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly FileSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {
    }

    /**
     * Get the list of files
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResults
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResults
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems(array_values($collection->getItems()));
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
