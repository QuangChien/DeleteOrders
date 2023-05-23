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
use Magento\Sales\Model\ResourceModel\OrderFactory;

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
     * @var OrderFactory
     */
    protected $orderResourceFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderRepository $orderRepository,
        Data $helper,
        OrderManagementInterface $orderManagement,
        OrderFactory $orderResourceFactory
    ) {
        parent::__construct($context, $filter);

        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->_orderManagement = $orderManagement;
        $this->orderResourceFactory = $orderResourceFactory;
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
                    $this->deleteOrderItem($order->getId());

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

    /**
     * @param int $orderId
     * @return void
     */
    public function deleteOrderItem($orderId)
    {
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        /** delete invoice grid*/
        $connection->delete(
            $resource->getTable('sales_invoice_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete shipment grid */
        $connection->delete(
            $resource->getTable('sales_shipment_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );

        /** delete creditmemo grid */
        $connection->delete(
            $resource->getTable('sales_creditmemo_grid'),
            $connection->quoteInto('order_id = ?', $orderId)
        );
    }
}
