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
     * @param Log $logResource
     * @param LogFactory $logFactory
     */
    public function __construct
    (
        Log $logResource,
        LogFactory $logFactory
    )
    {
        $this->logResource = $logResource;
        $this->logFactory = $logFactory;
    }

    /**
     * @inheirtDoc
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
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        //updating...
    }
}
