<?php namespace Decred\Payments\Model\Ui;

use Decred\Payments\Helper\Data;
use Magento\Checkout\Model\ConfigProviderInterface;
use Decred\Payments\Model\Payment;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * ConfigProvider constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                Payment::CODE => [
                    'isShowRefundAddress' => $this->helper->isShowRefundAddress(),
                    'isRefundAddressOptional'     => $this->helper->isRefundOptional(),
                ],
            ]
        ];
    }
}
