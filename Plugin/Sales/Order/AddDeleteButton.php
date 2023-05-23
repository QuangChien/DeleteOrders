<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Plugin\Sales\Order;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Victory\DeleteOrders\Helper\Data;

class AddDeleteButton
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param Data $helper
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Data $helper,
        AuthorizationInterface $authorization
    ) {
        $this->helper = $helper;
        $this->authorization = $authorization;
    }

    public function beforeSetLayout(View $object, LayoutInterface $layout)
    {
        if ($this->helper->isEnabled() && $this->authorization->isAllowed('Magento_Sales::delete')) {
            $object->addButton(
                'delete',
                [
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'sortOrder' => 50,
                    'onclick' => "confirmSetLocation('" . __('Are you sure you want to delete this order?') . "',
                               '{$object->getDeleteUrl()}')"
                ]
            );
        }

    return [$layout];
    }
}
