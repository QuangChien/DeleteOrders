<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Cron\Order;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use Victory\DeleteOrders\Helper\Data;
use Victory\DeleteOrders\Helper\Email;
use Psr\Log\LoggerInterface;

class Delete
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Email
     */
    protected $email;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @param Data $helperData
     * @param Email $email
     * @param StoreManagerInterface $storeManager
     * @param OrderRepository $orderRepository
     * @param State $state
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Data $helperData,
        Email $email,
        StoreManagerInterface $storeManager,
        OrderRepository $orderRepository,
        state $state,
        Registry $registry,
        LoggerInterface $logger,
        OrderManagementInterface $orderManagement
    ) {
        $this->helperData = $helperData;
        $this->email = $email;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->state = $state;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->orderManagement = $orderManagement;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();
            if (!$this->helperData->isEnabled($storeId)) {
                continue;
            }

            $orderCollection = $this->helperData->getMatchingOrders($storeId);
            if ($numOfOrders = $orderCollection->getSize()) {
                $this->registry->unregister('isSecureArea');
                $this->registry->register('isSecureArea', true);
                $errorOrders = [];

                foreach ($orderCollection->getItems() as $order) {
                    try {
                        if ($this->helperData->versionCompare('2.3.0')) {
                            if (in_array($order->getStatus(), ['processing', 'pending', 'fraud'], true)) {
                                $this->orderManagement->cancel($order->getId());
                            }
                            if ($order->getStatus() === 'holded') {
                                $this->orderManagement->unHold($order->getId());
                                $this->orderManagement->cancel($order->getId());
                            }
                        }
                        /** @var DataObject|OrderInterface $order */
                        $this->orderRepository->delete($order);
                        $this->helperData->deleteOrderItem($order->getId());
                    } catch (Exception $e) {
                        $errorOrders[$order->getId()] = $order->getIncrementId();
                        $this->logger->error($e->getMessage());
                    }
                }

                if ($this->email->isEnabledEmail($storeId)) {
                    if (!$this->state->getAreaCode()) {
                        $this->state->setAreaCode(Area::AREA_FRONTEND);
                    }

                    $templateParams = [
                        'num_order' => $numOfOrders,
                        'success_order' => $numOfOrders - count($errorOrders),
                        'error_order' => count($errorOrders)
                    ];

                    $this->email->sendEmailTemplate($templateParams, $storeId);
                }
            }
        }
    }
}
