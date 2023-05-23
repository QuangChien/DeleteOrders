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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\ConfigInterface;

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

    protected $productMetadata;

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
        ProductMetadataInterface $productMetadata
    )
    {
        $this->storeManager = $storeManager;
        $this->backendConfig = $backendConfig;
        $this->state = $state;
        $this->productMetadata = $productMetadata;
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
}
