<?php namespace Decred\Payments\Controller\Order;

use Magento\Framework\App\Action\Action;

class Status extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Decred\Payments\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Decred\Payments\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if ($incrementId = $this->request->getParam('order_id')) {
            if ($order = $this->helper->loadDecredOrderByIncrementId($incrementId)) {
                $result->setData([
                    'txid' => $order->getTxid(),
                    'status' => $order->getStatus(),
                    'confirmations' => $order->getConfirmations(),
                ]);
            }
        }

        return $result;
    }
}
