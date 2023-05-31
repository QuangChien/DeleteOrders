<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Api\Data;

interface LogInterface
{
    const ENTITY_ID = "entity_id";
    const ORDER_ID = "order_id";
    const ADMIN_USER = "admin_user";
    const DELETED_AT = "deleted_at";
    const DELETE_TYPE = "delete_type";

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return string|null
     */
    public function getAdminUser();

    /**
     * @param string $user
     * @return $this
     */
    public function setAdminUser($user);

    /**
     * @return string|null
     */
    public function getDeletedAt();

    /**
     * @param string $deletedAt
     * @return $this
     */
    public function setDeleteAt($deletedAt);

    /**
     * @return int
     */
    public function getDeleteType();

    /**
     * @param int $deleteType
     * @return $this
     */
    public function setDeleteType($deleteType);
}
