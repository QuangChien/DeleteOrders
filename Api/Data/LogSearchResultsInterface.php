<?php
/**
 * @author QuangChien(Glorious Victory) <quangchien01.it@gmail.com>
 * @copyright Copyright © 2023 QuangChien(Glorious Victory) <https://www.facebook.com/quangchien01>. All rights reserved.
 */

namespace Victory\DeleteOrders\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Victory\DeleteOrders\Api\Data\LogInterface[]
     */
    public function getItems();

    /**
     *
     * @param \Victory\DeleteOrders\Api\Data\LogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}