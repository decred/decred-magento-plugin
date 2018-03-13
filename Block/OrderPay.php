<?php namespace Decred\Payments\Block;

use Decred\Payments\Model\Order;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Helper\Data;

class OrderPay extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Decred\Payments\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricing;

    /**
     * @var \Decred\Payments\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * OrderPay constructor.
     *
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Decred\Payments\Model\OrderFactory              $orderFactory
     * @param \Decred\Payments\Helper\Data                     $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Decred\Payments\Model\OrderFactory $orderFactory,
        \Decred\Payments\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
        $this->helper = $helper;
    }

    /**
     * @return \Decred\Payments\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return float|string
     */
    public function getBaseTotal()
    {
        /** @var Data $pricing */
        $pricing = ObjectManager::getInstance()->create('\Magento\Framework\Pricing\Helper\Data');

        return $pricing->currency($this->getOrder()->getGrandTotal());
    }

    /**
     * @return Order
     */
    public function getDecredOrder()
    {
        /** @var Order $decredOrder */
        $decredOrder = $this->orderFactory->create();
        $decredOrder->load($this->getOrder()->getIncrementId(), 'order_id');

        return $decredOrder;
    }

    /**
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        if ($order = $this->registry->registry('current_order')) {
            return $order;
        }

        return  $this->checkoutSession->getLastRealOrder();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }
}
