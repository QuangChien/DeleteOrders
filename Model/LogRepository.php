<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Victory\DeleteOrders\Api\LogRepositoryInterface;
use Victory\DeleteOrders\Api\Data\LogInterface;
use Victory\DeleteOrders\Model\ResourceModel\Log;
use Victory\DeleteOrders\Model\LogFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Victory\DeleteOrders\Model\ResourceModel\Log\Collection;
use Victory\DeleteOrders\Model\ResourceModel\Log\CollectionFactory;
use Victory\DeleteOrders\Api\Data\LogSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

class LogRepository implements LogRepositoryInterface
{
    /**
     * @var Log
     */
    protected $logResource;

    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * @var LogSearchResultsInterfaceFactory
     */
    protected $logSearchResultsInterfaceFactory;

    /**
     * @var CollectionFactory
     */
    protected $logCollectionFactory;

    /**
     * @param Log $logResource
     * @param \Victory\DeleteOrders\Model\LogFactory $logFactory
     * @param LogSearchResultsInterfaceFactory $logSearchResultsInterfaceFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct
    (
        Log $logResource,
        LogFactory $logFactory,
        LogSearchResultsInterfaceFactory $logSearchResultsInterfaceFactory,
        CollectionFactory $collectionFactory
    )
    {
        $this->logResource = $logResource;
        $this->logFactory = $logFactory;
        $this->logSearchResultsInterfaceFactory = $logSearchResultsInterfaceFactory;
        $this->logCollectionFactory = $collectionFactory;
    }

    /**
     * @inheirtDoc
     * @throws NoSuchEntityException
     */
    public function save(LogInterface $log)
    {
        if ($log->getId()) {
            $newData = $log->getData();
            $log = $this->getById($log->getId());
            foreach ($newData as $k => $v) {
                $log->setData($k, $v);
            }
        }

        try {
            $this->logResource->save($log);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save order log: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        return $this->getById($log->getId());
    }

    /**
     * @inheirtDoc
     */
    public function getById($logId)
    {
        $log = $this->logFactory->create();
        $log->load($logId);

        if (!$log->getId()) {
            throw NoSuchEntityException::singleField('id', $logId);
        }
        return $log;
    }

    /**
     * @inheirtDoc
     */
    public function delete(LogInterface $log)
    {
        try {
            $logId = $log->getId();
            $this->logResource->delete($logId);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete order log with id %1',
                    $log->getId()
                ),
                $e
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($logId)
    {
        $log = $this->getById($logId);
        return $this->delete($log);
    }

    /**
     * @inheirtDoc
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->logSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /** @var Collection $collection */
        $collection = $this->logCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $logs = [];

        foreach ($collection as $log) {
            $logs[] = $this->getById($log->getId());
        }
        $searchResults->setItems($log);
        return $searchResults;
    }

    /**
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        foreach ($filterGroup->getFilters() as $filter) {
            $collection->addFieldToFilter($filter->getField(), $filter->getValue());
        }
    }
}
