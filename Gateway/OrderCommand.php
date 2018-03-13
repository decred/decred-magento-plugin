<?php namespace Decred\Payments\Gateway;

use Decred\Payments\Model\Total\DecredAmount;
use Decred\Payments\Observer\DataAssignObserver;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;

use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;

use Magento\Store\Model\StoreManagerInterface;

class OrderCommand implements CommandInterface
{
    /**
     * @var \Decred\Payments\Helper\Data
     */
    protected $helper;

    /**
     * @var \Decred\Payments\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Decred\Payments\Helper\Data $helper,
        \Decred\Payments\Model\OrderFactory $transactionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->orderFactory = $transactionFactory;
        $this->storeManager = $storeManager;
    }

    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObject $data */
        $data = $commandSubject['payment'];
        $incrementId = $data->getOrder()->getOrderIncrementId();
        /** @var Payment $payment */
        $payment = $data->getPayment();
        $order = $payment->getOrder();

        // $quote = $this->quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId())‌;
        /** @var Quote $quote */
        $quote = ObjectManager::getInstance()->create(Quote::class);
        $quote->loadByIdWithoutStore($order->getQuoteId());

        /** @var Quote\Address\Total $decredAmount */
        $decredAmount = $quote->getTotals()[DecredAmount::CODE];

        /** @var \Decred\Payments\Model\Order $decredOrder */
        $decredOrder = $this->orderFactory->create();
        $decredOrder->setData('order_id',       $incrementId);
        $decredOrder->setData('refund_address', $payment->getAdditionalInformation(DataAssignObserver::REFUND_ADDRESS));
        $decredOrder->setData('address',        $this->helper->getPaymentAddress($incrementId));
        $decredOrder->setData('amount',         $decredAmount->getData('value'));

        $decredOrder->setData('base_total',     $payment->getOrder()->getBaseGrandTotal());
        $decredOrder->setData('base_currency',  $payment->getOrder()->getBaseCurrencyCode());
        $decredOrder->setData('timestamp',      (new \DateTime())->getTimestamp());

        $decredOrder->save();
    }
}
