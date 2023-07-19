<?php
namespace Hyperbc\PaymentGateway\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Cancel extends Action {
    private $checkoutSession;
    protected $orderRepository;
    private $scopeConfig;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $order = $this->getOrder();
        // $orderId = $this->_request->getParam('order_id');
        // $order = $this->getOrder($orderId);

        if ($order->getId() && !$order->isCanceled()) {
            $failedStatus = $this->scopeConfig->getValue(
                'payment/hyperbc_paymentgateway/status_failed',
                ScopeInterface::SCOPE_STORE
            ); // \Magento\Sales\Model\Order::STATE_CANCELED
            $order->setStatus($failedStatus)->save();
        }

        $this->checkoutSession->restoreQuote();
        $this->_redirect('checkout/cart');
    }

    // private function getOrder($id): \Magento\Sales\Api\Data\OrderInterface
    // {
    //     return $this->orderRepository->get($id);
    // }
    private function getOrder(): \Magento\Sales\Model\Order
    {
        return $this->checkoutSession->getLastRealOrder();
    }
}
