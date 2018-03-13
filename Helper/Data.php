<?php namespace Decred\Payments\Helper;

use Decred\Payments\Model\Order;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Decred\Crypto\ExtendedKey;
use Decred\Rate\CoinMarketCap;

class Data extends AbstractHelper
{

    const XML_MASTER_PUBLIC_KEY = 'payment/decred_payments/master_public_key';

    const XML_CONFIRMATIONS_TO_WAIT = 'payment/decred_payments/confirmations_to_wait';

    const XML_SHOW_REFUND_ADDRESS = 'payment/decred_payments/show_refund_address';

    const XML_IS_REFUND_OPTIONAL = 'payment/decred_payments/is_refund_optional';

    /**
     * @var ExtendedKey
     */
    protected $extendedKey;

    /**
     * @return int
     */
    public function getConfirmationsToWait()
    {
        return $this->scopeConfig->getValue(
            static::XML_CONFIRMATIONS_TO_WAIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $currency
     * @param float  $amount
     *
     * @return float
     * @throws \Exception
     */
    public function convertToDecred($currency, $amount)
    {
        return CoinMarketCap::getRate($currency)->convertToCrypto($amount);
    }

    /**
     * @param string $incrementId
     *
     * @return Order
     */
    public function loadDecredOrderByIncrementId($incrementId)
    {
        $objectManager = ObjectManager::getInstance();

        /** @var Order $decredOrder */
        $decredOrder = $objectManager->create('Decred\Payments\Model\Order');
        $decredOrder->load(intval(ltrim($incrementId, '0')), 'order_id');

        return $decredOrder;
    }

    /**
     * @return ExtendedKey
     */
    public function extendedKey()
    {
        if ($this->extendedKey === null) {
            $this->extendedKey = ExtendedKey::fromString($this->masterPublicKey());
        }

        return $this->extendedKey;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    public function getPaymentAddress($index)
    {
        return $this->extendedKey()
            ->publicChildKey(0)
            ->publicChildKey($index)
            ->getAddress();
    }

    /**
     * @return bool
     */
    public function isShowRefundAddress()
    {
        return (bool) $this->scopeConfig->getValue(
            static::XML_SHOW_REFUND_ADDRESS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isRefundOptional()
    {
        return (bool) $this->scopeConfig->getValue(
            static::XML_IS_REFUND_OPTIONAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    protected function masterPublicKey()
    {
        return $this->scopeConfig->getValue(
            static::XML_MASTER_PUBLIC_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }
}
