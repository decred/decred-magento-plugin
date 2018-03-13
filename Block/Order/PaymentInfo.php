<?php namespace Decred\Payments\Block\Order;

use Decred\Payments\Model\Order;
use Magento\Payment\Block\Info;

use Magento\Framework\View\Element\Template\Context;

class PaymentInfo extends Info
{

    protected $orderFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricing;

    /**
     * @var \Decred\Payments\Helper\Data
     */
    protected $helper;

    public function __construct(
        Context $context,
        \Decred\Payments\Model\OrderFactory $orderFactory,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        \Decred\Payments\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderFactory = $orderFactory;
        $this->pricing = $pricing;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Sales\Model\Order\Payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfo()
    {
        return parent::getInfo();
    }

    public function getSpecificInformation()
    {
        /** @var Order $decredOrder */
        $decredOrder = $this->orderFactory->create();
        $decredOrder->load($this->getInfo()->getOrder()->getIncrementId(), 'order_id');

        $information = [
            'Total DCR' => $decredOrder->getAmount(),
            'Address' => $decredOrder->getAddress(),
            'Status' => $decredOrder->getStatusText(),
            'Txid' => $decredOrder->getTxid(),
            'Confirmations' => $decredOrder->getConfirmations(),
            'Refund' => $decredOrder->getRefundAddress(),
        ];

        return $information;
    }
}
