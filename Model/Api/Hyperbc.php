<?php

namespace Hyperbc\PaymentGateway\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

class Hyperbc
{

    /** @var string */
    private $_appId = "";

    /** @var string */
    private $_version = "1.0";
    /** @var string */
    private $_language = "1.0";

    /** @var ScopeConfigInterface */
    private $_scopeConfig;

    /** @var Signature */
    private $_signature;

    /** @var string */
    private $_baseUrl;
    protected $_store;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Signature $signature,
        StoreInterface $store
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_appId = $this->_scopeConfig->getValue(
            'payment/hyperbc_paymentgateway/app_id',
            ScopeInterface::SCOPE_STORE
        );
        $this->_store = $store;

        $storeLanguage = $this->_store->getLocaleCode();
        if ($storeLanguage == 'ko_KR') {
            $this->_language = "ko";
        } else if ($storeLanguage == 'zh_CN' || $storeLanguage == 'zh_HK') {
            $this->_language = "zh";
        } else {
            $this->_language = "en";
        }

        $this->_signature = $signature;

        $environment = $this->_scopeConfig->getValue('payment/hyperbc_paymentgateway/environment', ScopeInterface::SCOPE_STORE);

        $this->_baseUrl = $environment == 'Sandbox'
            ? 'http://apitest.hyperbc.top/shopapi/'
            : 'https://api.hyperbc.top/shopapi/';
    }

    public function request($url, $method = 'POST', $params = array())
    {
        $params = json_encode($params);
        $url = $this->_baseUrl . $url;
        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($params)
        ];
        $curl = curl_init();
        $SSL = substr($url, 0, 8) == "https://" ? TRUE : FALSE;

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($SSL) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = json_decode(curl_exec($curl), TRUE);
        error_log(print_r($response, true));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (array_key_exists('data', $response)) {
            return $response;
        } else {
            throw new \Exception('Invalid http response ' . $http_status);
        }
    }

    private function sign($obj)
    {
        $obj["app_id"] = $this->_appId;
        $obj["version"] = $this->_version;
        $obj["lang"] = $this->_language;
        $obj["time"] = strval(time());
        $obj['sign'] = $this->_signature->encryption($obj);

        return $obj;
    }

    private function checkSign($sign, $data)
    {
        return $this->_signature->checkSignature($sign, $data);
    }

    public function createOrFail($params)
    {
        $order = $this->request('h5_order/create', 'POST', $this->sign($params));

        if ($order["status"] != 200) {
            throw new \Exception('OrderIDDuplicate');
            return;
        }

        if ($this->checkSign($order["sign"], $order)) {
            return $order["data"];
        }
        throw new \Exception('Unknown case.');
    }

}