<?php

namespace Hyperbc\PaymentGateway\Model\Api;

use Hyperbc\PaymentGateway\Api\UpdateStatusOrderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\ScopeInterface;

class UpdateStatusOrder implements UpdateStatusOrderInterface
{
    /** @var OrderRepositoryInterface */
    private $_orderRepository;

    /** @var Request */
    protected $_request;

    /** @var ScopeConfigInterface */
    private $_scopeConfig;

    /** @var Signature */
    private $_signature;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig,
        Request $request,
        Signature $signature
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
        $this->_signature = $signature;
    }

    /**
     * Postback Hyperbc
     * @return string
     */
    public function doUpdate()
    {
        try {
            $request = file_get_contents('php://input');
            
            $body = json_decode($request, true);
            error_log(print_r($body, true));
            if (!$this->validateSignature($body)) {
                return 'Signature validation failed.';
            }

            $data = $body['data'];

            $merchant_order_id = explode('p', $data['merchant_order_id'])[1];
            $order = $this->getOrder($merchant_order_id);
            $this->updateOrderStatus($order, $data);

            $response = array(
                'status' => 200,
                'data' => array(
                    'success_data' => 'success'
                ),
                'sign' => ''
            );
            $response = $this->signData($response);

            return $response;
        } catch (\Exception $e) {
            return 'Webhook receive error.';
        }
    }

    public function updateOrderStatus($order, $data)
    {
        if ($data['status'] == 0) { // pending payment
            $pendingStatus = $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/status_pending',
                ScopeInterface::SCOPE_STORE
            );
            $order->setStatus($pendingStatus)->save();
            return;
        } else if ($data['status'] == 1) { // 已完成
            $pendingStatus = $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/status_completed',
                ScopeInterface::SCOPE_STORE
            );
            $order->setStatus($pendingStatus)->save();
            return;
        } else if ($data['status'] == 2) { // 异常支付
            $failedStatus = $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/status_abnormal',
                ScopeInterface::SCOPE_STORE
            );
            $order->setStatus($failedStatus)->save();
            $order->save();
        } else if ($data['status'] == 10) { // 已取消
            $failedStatus = $this->_scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/status_failed',
                ScopeInterface::SCOPE_STORE
            );
            $order->setStatus($failedStatus)->save();
            $order->save();
        }
    }

    private function getOrder($id):\Magento\Sales\Api\Data\OrderInterface
    {
        return $this->_orderRepository->get($id);
    }

    /**
     * Validate signature of the request recevied from webhook
     */
    private function validateSignature($body)
    {
        return $this->_signature->checkSignature($body["sign"], $body["data"]);
    }

    private function signData($body)
    {
        return $this->_signature->encryption($body);
    }
}