<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Country implements ArrayInterface
{
    const SPECIFIC = '1';
    const ALL      = '2';

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ALL, 'label' => __('All Countries')],
            ['value' => self::SPECIFIC, 'label' => __('Specific Countries')]
        ];
    }
}
