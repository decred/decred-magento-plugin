<?php namespace Decred\Payments\Model\Total;

use \Decred\Payments\Helper\Data;
use Magento\Framework\App\ObjectManager;

class DecredAmount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    const CODE = 'decred_amount';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Decred amount');
    }

    /**
     * DecredAmount constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $grandTotal = $quote->getBaseGrandTotal();
        $currency = $quote->getBaseCurrencyCode();

        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => $grandTotal ? $this->helper->convertToDecred($currency, $grandTotal) : 0,
        ];
    }
}
