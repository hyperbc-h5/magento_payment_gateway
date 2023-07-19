<?php

namespace Hyperbc\PaymentGateway\Model\Payment;

class Hyperbcmethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'hyperbc_paymentgateway';

    protected $_code = 'hyperbc_paymentgateway';

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {

        $app_id = $this->_scopeConfig->getValue(
            'payment/hyperbc_paymentgateway/app_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $public_key = $this->_scopeConfig->getValue(
            'payment/hyperbc_paymentgateway/public_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $private_key = $this->_scopeConfig->getValue(
            'payment/hyperbc_paymentgateway/private_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$app_id || !$public_key ||  !$private_key) {
            return false;
        }
        return parent::isAvailable($quote);
    }
}
