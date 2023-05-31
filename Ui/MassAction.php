<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Ui;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\AuthorizationInterface;
use Victory\DeleteOrders\Helper\Data;

class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * Order action type
     */
    const ACTION_TYPE = "delete";

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param ContextInterface $context
     * @param Data $helper
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Data $helper,
        AuthorizationInterface $authorization,
        $components,
        array $data
    ) {
        $this->helper = $helper;
        $this->authorization = $authorization;
        parent::__construct($context, $components, $data);
    }

    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();
        foreach ($config['actions'] as $key => $action) {
            if(self::ACTION_TYPE == $action['type']) {
                if(!$this->helper->isEnabled() || !$this->authorization->isAllowed('Magento_Sales::delete')) {
                    unset($config['actions'][$key]);
                }
            }
        }
        $this->setData('config', $config);
    }
}
