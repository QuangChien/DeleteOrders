<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Controller\Adminhtml\Delete;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use Victory\DeleteOrders\Helper\Data;
use Psr\Log\LoggerInterface;

class Manually extends Action
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param OrderRepository $orderRepository
     * @param OrderManagementInterface $orderManagement
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Data $helperData,
        OrderRepository $orderRepository,
        OrderManagementInterface $orderManagement,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
        $this->orderManagement = $orderManagement;
        $this->storeManager = $storeManager;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId = $this->getRequest()->getParam('store');
        $orderCollection = $this->helperData->getMatchingOrders($storeId);

        if ($orderCollection->getSize()) {
            $successDelete = 0;
            $errorOrders = [];

            foreach ($orderCollection->getItems() as $order) {
                try {
                    if ($this->helperData->versionCompare('2.3.0')) {
                        if (in_array($order->getStatus(), ['processing', 'pending', 'fraud'], true)) {
                            $this->orderManagement->cancel($order->getId());
                        }
                        if ($order->getStatus() == 'holded') {
                            $this->orderManagement->unHold($order->getId());
                            $this->orderManagement->cancel($order->getId());
                        }
                    }
                    /** @var DataObject|OrderInterface $order */
                    $this->orderRepository->delete($order);

                    $this->helperData->deleteOrderItem($order->getId());

                    /** inset log */
                    $this->helperData->insertLog([
                        'admin_user' => $this->helperData->getUserId(),
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
                    $successDelete++;
                } catch (Exception $e) {
                    $errorOrders[$order->getId()] = $order->getIncrementId();
                    $this->logger->error($e->getMessage());
                }
            }

            if ($successDelete) {
                $this->messageManager->addSuccessMessage(__("Success! " . $successDelete . "orders have been deleted."));
            }

            if (count($errorOrders)) {
                $this->messageManager->addErrorMessage(__(
                    'The following orders cannot being deleted. %1',
                    implode(', ', $errorOrders)
                ));
            }
        } else {
            $this->messageManager->addNoticeMessage(__('No order has been deleted!'));
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
