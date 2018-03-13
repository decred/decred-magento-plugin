<?php namespace Decred\Payments\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            '\Decred\Payments\Model\Order',
            '\Decred\Payments\Model\ResourceModel\Order'
        );
    }
}
