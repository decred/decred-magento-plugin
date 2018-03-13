<?php
/**
 * Decred payment model
 */
namespace Decred\Payments\Model;

use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Adapter;

class Payment extends Adapter
{
    /**
     * Magneto payment method
     */
    const CODE = 'decred_payments';
}
