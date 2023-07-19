<?php

namespace Hyperbc\PaymentGateway\Controller\Payment;

use Magento\Framework\App\ActionInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Hyperbc\PaymentGateway\Model\Api\Hyperbc;

class Create implements ActionInterface
{
    private $checkoutSession;
    private $resultJsonFactory;
    private $scopeConfig;
    protected $urlBuilder;
    protected $widgetKey;
    private $_hyperbc;

    public function __construct(
        Session $checkoutSession,
        JsonFactory $resultJsonFactory,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        Hyperbc $hyperbc
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_hyperbc = $hyperbc;
    }

    public function execute()
    {
        $order = $this->getOrder();
        $pendingStatus = $this->scopeConfig->getValue(
            'payment/hyperbc_paymentgateway/status_pending',
            ScopeInterface::SCOPE_STORE); // \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT
        $order->setStatus($pendingStatus)->save();

        $urlParams = $this->getUrlParameters($order);

        $hyperbc_order = $this->_hyperbc->createOrFail($urlParams);
        if (!$hyperbc_order || !$hyperbc_order["order_no"]) {
            throw new \Exception('Order #' . $order->getId() . ' does not exists');
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData(['redirectUrl' => $hyperbc_order["checkout_url"]]);
    }

    private function getUrlParameters($order): array {
        
        $customId = $order->getId();
        $orderAmount = $order->getGrandTotal();
        // $orderCurrency = $order->getOrderCurrencyCode();

        $data['merchant_order_id'] = "p".$customId;
        $data['amount'] = $orderAmount;
        $data['currency'] = 'usd';
        // $data['customId'] = 'magento_order_' . $customId;
        // $data['priceCurrency'] = $orderCurrency;
        // $data['priceAmount'] = $orderAmount;
        $data['return_url'] = $this->urlBuilder->getUrl(
            'hyperbc/payment/success?id='.$customId,
            ['_query' => ['order_id' => $order->getId()]]
        );
        // $data['unsuccessRedirectUrl'] = $this->urlBuilder->getUrl(
        //     'hyperbc/payment/cancel',
        //     ['_query' => ['order_id' => $order->getId()]]
        // );

        return $data;
    }


    private function getOrder(): \Magento\Sales\Model\Order
    {
        return $this->checkoutSession->getLastRealOrder();
    }
}
