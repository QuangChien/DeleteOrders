<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Model\ResourceModel\Log;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Victory\DeleteOrders\Model\ResourceModel\Log as LogResource;
use Victory\DeleteOrders\Model\Log as LogModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Initialize collection.
     */
    protected function _construct()
    {
        $this->_init(LogModel::class, LogResource::class);
    }
}
