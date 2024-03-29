<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Api;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Victory\DeleteOrders\Api\Data\LogInterface;
use Victory\DeleteOrders\Api\Data\LogSearchResultsInterface;

interface LogRepositoryInterface
{
    /**
     * @param \Victory\DeleteOrders\Api\Data\LogInterface $log
     * @return \Victory\DeleteOrders\Api\Data\LogInterface
     * @throws LocalizedException
     */
    public function save(LogInterface $log);

    /**
     * @param int $logId
     * @return \Victory\DeleteOrders\Api\Data\LogInterface
     * @throws LocalizedException
     */
    public function getById($logId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Victory\DeleteOrders\Api\Data\LogSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);


    /**
     * @param \Victory\DeleteOrders\Api\Data\LogInterface $log
     * @return bool
     * @throws LocalizedException
     */
    public function delete(LogInterface $log);

    /**
     * @param int $logId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($logId);
}
