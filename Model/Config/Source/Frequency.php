<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model\Config\Source;

class Frequency extends \Magento\Cron\Model\Config\Source\Frequency
{
    /**
     * Disable status
     */
    const DISABLE = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        parent::toOptionArray();
        array_unshift(self::$_options, ['label' => __('Disable'), 'value' => self::DISABLE]);

        return self::$_options;
    }
}
