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
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheirtDoc
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
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

    /**
     * @inheirtDoc
     */
    public function getCustomerEmail(){
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheirtDoc
     */
    public function setCustomerEmail($email){
        return $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * @inheirtDoc
     */
    public function getFirstname(){
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @inheirtDoc
     */
    public function setFirstname($firstname){
        return $this->setData(self::CUSTOMER_FIRSTNAME, $firstname);
    }

    /**
     * @inheirtDoc
     */
    public function getLasttname(){
        return $this->getData(self::CUSTOMER_LASTNAME);
    }

    /**
     * @inheirtDoc
     */
    public function setLastname($lastname){
        return $this->setData(self::CUSTOMER_LASTNAME, $lastname);
    }

    /**
     * @inheirtDoc
     */
    public function getOrderDate(){
        return $this->getData(self::ORDER_DATE);
    }

    /**
     * @inheirtDoc
     */
    public function setOrderDate($orderDate){
        return $this->setData(self::ORDER_DATE, $orderDate);
    }

    /**
     * @inheirtDoc
     */
    public function getOrderStatus(){
        return $this->getData(self::ORDER_STATUS);
    }

    /**
     * @inheirtDoc
     */
    public function setOrderStatus($orderStatus){
        return $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    /**
     * @inheirtDoc
     */
    public function getSubtotal(){
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheirtDoc
     */
    public function setSubtotal($subtotal){
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * @inheirtDoc
     */
    public function getGrandTotal(){
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheirtDoc
     */
    public function setGrandTotal($grandTotal){
        return $this->setData(self::GRAND_TOTAL, $grandTotal);
    }

    /**
     * @inheirtDoc
     */
    public function getTotalDue(){
        return $this->getData(self::TOTAL_DUE);
    }

    /**
     * @inheirtDoc
     */
    public function setTotalDue($totalDue){
        return $this->setData(self::TOTAL_DUE, $totalDue);
    }
}
