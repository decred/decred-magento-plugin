<?php namespace Decred\Payments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Order extends AbstractDb
{
    public function _construct()
    {
        $this->_init('decred_payments_order', 'id');
    }
}
