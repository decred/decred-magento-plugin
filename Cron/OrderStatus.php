<?php namespace Decred\Payments\Cron;

use Decred\Payments\Helper\Data;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

use Decred\Payments\Model\Order;
use Decred\Data\Transaction;

class OrderStatus
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * OrderStatus constructor.
     *
     * @param LoggerInterface $logger
     * @param Data            $helper
     */
    public function __construct(LoggerInterface $logger, Data $helper)
    {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     *
     */
    public function execute()
    {
        $waitingOrders = $this->createOrdersCollection();

        /** @var Order $decredOder */
        foreach ($waitingOrders as $decredOder) {
            $address = $decredOder->getAddress();
            $timestamp = $decredOder->getTimestamp();

            $transactions = $this->helper->extendedKey()
                ->getNetwork()
                ->getDataClient()
                ->getAddressRaw($address, $timestamp);

            if (is_array($transactions)) {
                foreach ($transactions as $transaction) {
                    $this->processTransaction($decredOder, $transaction);
                }
            }
        }
    }

    /**
     * @param Order       $decredOrder
     * @param Transaction $transaction
     */
    protected function processTransaction(Order $decredOrder, Transaction $transaction)
    {
        switch ($decredOrder->getStatus()) {
            case Order::STATUS_PENDING:
                if ($transaction->getOutAmount($decredOrder->getAddress()) >= $decredOrder->getAmount()) {
                    $this->logger->info('testing');

                    $decredOrder->setData('txid', $transaction->getTxid());
                    $decredOrder->setData('confirmations', $transaction->getConfirmations());
                    $decredOrder->setData('status', Order::STATUS_PROCESSING);
                    $decredOrder->save();

                }
                break;
            case Order::STATUS_PROCESSING:

                if ($transaction->getTxid() === $decredOrder->getTxid()) {
                    if ($transaction->getConfirmations() >= $decredOrder->getConfirmations()) {

                        $decredOrder->setData('confirmations', $transaction->getConfirmations());
                        $decredOrder->save();

                        if ($decredOrder->getConfirmations() >= $this->helper->getConfirmationsToWait()) {
                            $decredOrder->capture();
                            $decredOrder->setData('status', Order::STATUS_PAID);
                            $decredOrder->save();
                        }

                    }
                }

                break;
            default:
                break;
        }
    }

    /**
     * @return AbstractCollection $waiting
     */
    protected function createOrdersCollection()
    {
        /** @var AbstractCollection $collection */
        $collection = ObjectManager::getInstance()->create('Decred\Payments\Model\ResourceModel\Order\Collection');
        $collection->addFieldToSelect('*')
            ->addFieldToFilter('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])
            ->load();

        return $collection;
    }
}
