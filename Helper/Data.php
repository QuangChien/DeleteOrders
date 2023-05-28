<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\ConfigInterface;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Mageplaza\DeleteOrders\Model\Config\Source\Country;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Data extends AbstractHelper {

    /**
     * Path config of module
     */
    const MODULE_CONFIG_PATH = 'delete_orders';

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ConfigInterface
     */
    protected $backendConfig;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var OrderFactory
     */
    protected $orderResourceFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $backendConfig
     * @param State $state
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager,
        ConfigInterface $backendConfig,
        State $state,
        ProductMetadataInterface $productMetadata,
        OrderFactory $orderResourceFactory,
        CollectionFactory $orderCollectionFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->backendConfig = $backendConfig;
        $this->state = $state;
        $this->productMetadata = $productMetadata;
        $this->orderResourceFactory = $orderResourceFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $storeId
     * @return array|mixed
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId);
    }

    /**
     * @param $code
     * @param $storeId
     * @return array|mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::MODULE_CONFIG_PATH . '/general' . $code, $storeId);
    }

    /**
     * @param $field
     * @param $scopeValue
     * @param $scopeType
     * @return mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($scopeValue === null && !$this->isArea()) {
            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * @param string $area
     * @return bool|mixed
     */
    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            try {
                $this->isArea[$area] = ($this->state->getAreaCode() == $area);
            } catch (\Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }

    /**
     * @param $ver
     * @param $operator
     * @return bool|int
     */
    public function versionCompare($ver, $operator = '>=')
    {
        $version = $this->productMetadata->getVersion();
        return version_compare($version, $ver, $operator);
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

    public function getMatchingOrders($storeId = null, $limit = 1000)
    {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('status', ['in' => $this->getOrderStatusConfig($storeId)])
            ->addFieldToFilter('customer_group_id', ['in' => $this->getOrderCustomerGroupConfig($storeId)]);

        $storeIds = $this->getStoreViewConfig($storeId);
        if (!in_array(Store::DEFAULT_STORE_ID, $storeIds, true)) {
            $orderCollection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        if ($total = $this->getOrderTotalConfig($storeId)) {
            $orderCollection->addFieldToFilter('base_grand_total', ['lteq' => $total]);
        }

        if ($limit) {
            $orderCollection->getSelect()->limit($limit);
        }

        if ($this->getShippingCountryType($storeId) === Country::SPECIFIC) {
            $orderCollection->getSelect()
                ->joinLeft(
                    ['soa' => $orderCollection->getTable('sales_order_address')],
                    'main_table.entity_id = soa.parent_id',
                    []
                )
                ->where('soa.country_id IN (?)', $this->getCountriesConfig($storeId));
        }

        return $orderCollection;
    }

    /**
     * @param $storeId
     * @return string[]
     */
    public function getOrderStatusConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('order_status', $storeId) ?? "");
    }

    /**
     * @param $code
     * @param $storeId
     * @return mixed
     */
    public function getScheduleConfig($code, $storeId = null)
    {
        return $this->getModuleConfig('schedule/' . $code, $storeId);
    }

    /**
     * @param $field
     * @param $storeId
     * @return mixed
     */
    public function getModuleConfig($field = '', $storeId = null)
    {
        $field = ($field !== '') ? '/' . $field : '';

        return $this->getConfigValue(static::MODULE_CONFIG_PATH . $field, $storeId);
    }

    /**
     * @param $storeId
     * @return string[]
     */
    public function getOrderCustomerGroupConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('customer_groups', $storeId) ?? "");
    }

    /**
     * @param $storeId
     * @return string[]
     */
    public function getStoreViewConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('store_views', $storeId) ?? "");
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getOrderTotalConfig($storeId = null)
    {
        return $this->getScheduleConfig('order_total', $storeId);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShippingCountryType($storeId = null)
    {
        return $this->getScheduleConfig('country', $storeId);
    }

    /**
     * @param $storeId
     * @return string[]
     */
    public function getCountriesConfig($storeId = null)
    {
        return explode(',', $this->getScheduleConfig('specific_country', $storeId) ?? "");
    }
}
