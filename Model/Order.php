<?php namespace Decred\Payments\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\DB\Transaction;

class Order extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{

    const CACHE_TAG = 'decred_payments_model';

    const STATUS_PAID = 1;

    const STATUS_PROCESSING = 2;

    const STATUS_PENDING = 3;

    /**
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * Construct model.
     */
    protected function _construct()
    {
        $this->_init('\Decred\Payments\Model\ResourceModel\Order');
    }

    public function isPaid()
    {
        return $this->getStatus() === static::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return $this->getStatus() === static::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->getStatus() === static::STATUS_PENDING;
    }

    /**
     * @throws bool
     */
    public function capture()
    {
        $objectManager = ObjectManager::getInstance();

        /** @var \Magento\Sales\Model\Order $order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId($this->getOrderId());

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $objectManager->create('Magento\Sales\Model\Order\Payment');
        $payment->load($order->getId());
        $payment->setOrder($order);
        $order->setPayment($payment);

        $payment->capture();

        /** @var Transaction $transaction */
        $transaction = $objectManager->create('\Magento\Framework\DB\Transaction');
        // $transaction->addObject($invoice);
        $transaction->addObject($order);
        $transaction->addObject($payment);
        $transaction->save();
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [static::CACHE_TAG.' '.$this->getId()];
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->getData('address');
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return floatval($this->getData('amount'));
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        if ($this->timestamp === null) {
            $this->timestamp = new \DateTime();
            $this->timestamp->setTimestamp((int) $this->getData('timestamp'));
        }

        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->getData('status');
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        switch ($this->getStatus()) {
            case static::STATUS_PAID:
                return 'Paid';
                break;
            case static::STATUS_PENDING:
                return 'Pending';
                break;
            case static::STATUS_PROCESSING:
                return 'Processing';
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * @return string
     */
    public function getTxid()
    {
        return (string) $this->getData('txid');
    }

    /**
     * @return int
     */
    public function getConfirmations()
    {
        return (int) $this->getData('confirmations');
    }

    /**
     * @return float
     */
    public function getBaseTotal()
    {
        return (float) $this->getData('base_total');
    }

    /**
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->getData('base_currency');
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return (int) $this->getData('order_id');
    }

    /**
     * @return string
     */
    public function getRefundAddress()
    {
        return $this->getData('refund_address');
    }
}
