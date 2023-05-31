<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model;

use Victory\DeleteOrders\Api\Data\LogInterface;
use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel implements LogInterface
{
    /**
     * Initialize model.
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Log::class);
    }

    /**
     * @inheirtDoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheirtDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheirtDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheirtDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheirtDoc
     */
    public function getAdminUser()
    {
        return (string)$this->getData(self::ADMIN_USER);
    }

    /**
     * @inheirtDoc
     */
    public function setAdminUser($user)
    {
        return $this->setData(self::ADMIN_USER, $user);
    }

    /**
     * @inheirtDoc
     */
    public function getDeletedAt()
    {
        return $this->getData(self::DELETED_AT);
    }

    /**
     * @inheirtDoc
     */
    public function setDeleteAt($deletedAt)
    {
        return $this->setData(self::DELETED_AT, $deletedAt);
    }

    /**
     * @inheirtDoc
     */
    public function getDeleteType()
    {
        return $this->getData(self::DELETE_TYPE);
    }

    /**
     * @inheirtDoc
     */
    public function setDeleteType($deleteType)
    {
        return $this->setData(self::DELETE_TYPE, $deleteType);
    }
}
