<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Controller\Adminhtml\Order;
use Victory\DeleteOrders\Helper\Data;

class Delete extends Order
{
    /**
     * Authorization
     */
    const ADMIN_RESOURCE = 'Magento_Sales::delete';

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect  = $this->resultRedirectFactory->create();
        $helper = $this->_objectManager->get(Data::class);
        if (!$helper->isEnabled()) {
            $this->messageManager->addError(__('Cannot delete the order.'));

            return $resultRedirect->setPath('sales/order/view', [
                'order_id' => $this->getRequest()->getParam('order_id')
            ]);
        }

        $order = $this->_initOrder();
        if ($order) {
            try {
                if ($helper->versionCompare('2.3.0')) {
                    if (in_array($order->getStatus(), ['processing', 'pending', 'fraud'])) {
                        $this->orderManagement->cancel($order->getId());
                    }
                    if ($order->getStatus() === 'holded') {
                        $this->orderManagement->unHold($order->getId());
                        $this->orderManagement->cancel($order->getId());
                    }
                }

                /** delete order*/
                $this->orderRepository->delete($order);

                /** delete order data on grid */
                $helper->deleteOrderItem($order->getId());

                /** insert log */
                $helper->insertLog([
                    'admin_user' => $helper->getUserId(),
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

                $this->messageManager->addSuccessMessage(__('The order has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('sales/order/view', ['order_id' => $order->getId()]);
            }
        }

        return $resultRedirect->setPath('sales/*/');
    }
}
