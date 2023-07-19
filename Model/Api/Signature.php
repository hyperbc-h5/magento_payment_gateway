<?php

namespace Hyperbc\PaymentGateway\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Signature
{
    /** @var ScopeConfigInterface */
    private $_scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Sign the request data
     *
     * @param $data
     * @return string
     */
    public function encryption($data)
    {
        if (is_array($data)) {
            $signString = $this->getSignString($data);
        } else
            $signString = $data;
        $privKeyId = openssl_pkey_get_private(
            $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/private_key',
                ScopeInterface::SCOPE_STORE
            )
        );
        $signature = '';
        openssl_sign($signString, $signature, $privKeyId, OPENSSL_ALGO_MD5);
        return base64_encode($signature);
    }

    /**
     * Sign the request data
     *
     * @param $sign
     * @param $data
     * @return bool
     */
    public function checkSignature($sign, $data)
    {
        $toSign = $this->getSignString($data);
        $publicKeyId = openssl_pkey_get_public(
            $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/public_key',
                ScopeInterface::SCOPE_STORE
            )
        );

        $result = openssl_verify($toSign, base64_decode($sign), $publicKeyId, OPENSSL_ALGO_MD5);
        return $result === 1 ? true : false;
    }

    /**
     * Util method to preparing the sign string
     * @return string
     */
    private function getSignString($data)
    {
        unset($data['sign']);
        ksort($data);
        reset($data);
        $pairs = array();
        foreach ($data as $k => $v) {
            if (is_array($v))
                $v = $this->arrayToString($v);
            $pairs[] = "$k=$v";
        }
        return implode('&', $pairs);
    }

    /**
     * Util method to sort the map data as require by signature
     * @return string
     */
    private function arrayToString($data)
    {
        $str = '';
        foreach ($data as $list) {
            if (is_array($list)) {
                $str .= $this->arrayToString($list);
            } else {
                $str .= $list;
            }
        }
        return $str;
    }
}