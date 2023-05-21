<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Plugin\Sales\Order;

use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

class AddDeleteButton
{

    public function beforeSetLayout(View $object, LayoutInterface $layout)
    {
        $object->addButton(
            'delete',
            [
                'label'     => __('Delete'),
                'class'     => 'delete',
                'sortOrder' => 50,
                'onclick'   => "confirmSetLocation('" . __('Are you sure you want to delete this order?') ."',
                               '{$object->getDeleteUrl()}')"
            ]
        );

    return [$layout];
    }
}
