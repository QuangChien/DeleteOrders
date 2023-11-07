<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Api\Data;

interface LogInterface
{
    const ENTITY_ID = "entity_id";
    const INCREMENT_ID = "order_id";
    const ADMIN_USER = "admin_user";
    const DELETED_AT = "deleted_at";
    const DELETE_TYPE = "delete_type";

    const CUSTOMER_EMAIL = "customer_email";
    const CUSTOMER_FIRSTNAME = "customer_firstname";
    const CUSTOMER_LASTNAME = "customer_lastname";
    const ORDER_DATE = "order_date";
    const ORDER_STATUS = "order_status";
    const SUBTOTAL = "subtotal";
    const GRAND_TOTAL = "grand_total";
    const TOTAL_DUE = "total_due";

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
    public function getIncrementId();

    /**
     * @param int $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId);

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

    /**
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail($email);

    /**
     * @return string|null
     */
    public function getFirstname();

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname);

    /**
     * @return string|null
     */
    public function getLasttname();

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname);

    /**
     * @return string|null
     */
    public function getOrderDate();

    /**
     * @param string $orderDate
     * @return $this
     */
    public function setOrderDate($orderDate);

    /**
     * @return string|null
     */
    public function getOrderStatus();

    /**
     * @param string $orderStatus
     * @return $this
     */
    public function setOrderStatus($orderStatus);

    /**
     * @return float|null
     */
    public function getSubtotal();

    /**
     * @param int $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal);

    /**
     * @return float|null
     */
    public function getGrandTotal();

    /**
     * @param int $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal);

    /**
     * @return float|null
     */
    public function getTotalDue();

    /**
     * @param int $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue);

}
