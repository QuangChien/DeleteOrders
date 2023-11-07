<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Victory\DeleteOrders\Helper\Data;

class MassDelete extends AbstractMassAction
{
    /**
     * Authorization
     */
    const ADMIN_RESOURCE = 'Magento_Sales::delete';

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var OrderManagementInterface
     */
    protected $_orderManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderRepository $orderRepository
     * @param Data $helper
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderRepository $orderRepository,
        Data $helper,
        OrderManagementInterface $orderManagement
    ) {
        parent::__construct($context, $filter);

        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->_orderManagement = $orderManagement;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        if ($this->helper->isEnabled()) {
            $deleted = 0;

            /** @var OrderInterface $order */
            foreach ($collection->getItems() as $order) {
                try {
                    if ($this->helper->versionCompare('2.3.0')) {
                        if (in_array($order->getStatus(), ['processing', 'pending', 'fraud'], true)) {
                            $this->_orderManagement->cancel($order->getId());
                        }
                        if ($order->getStatus() === 'holded') {
                            $this->_orderManagement->unHold($order->getId());
                            $this->_orderManagement->cancel($order->getId());
                        }
                    }

                    /** delete order*/
                    $this->orderRepository->delete($order);

                    /** delete order data on grid report data related*/
                    $this->helper->deleteOrderItem($order->getId());

                    /** insert log */
                    $this->helper->insertLog([
                        'admin_user' => $this->helper->getUserId(),
                        'delete_type' => 1, //manually
                        'increment_id' => $order->getIncrementId(),
                        'customer_email' => $order->getCustomerEmail(),
                        'customer_firstname' => $order->getCustomerFirstname(),
                        'customer_lastname' => $order->getCustomerLastname(),
                        'order_date' => $order->getCreatedAt(),
                        'order_status' =>$order->getStatus(),
                        'subtotal' => $order->getSubtotal(),
                        'grand_total' => $order->getGrandTotal(),
                        'total_due' => $order->getTotalDue()
                    ]);

                    $deleted++;
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage(__(
                        'Cannot delete order #%1. Please try again later.',
                        $order->getIncrementId()
                    ));
                }
            }

            if ($deleted) {
                $this->messageManager->addSuccessMessage(__('A total of %1 order(s) has been deleted.', $deleted));
            }
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
