<?php

namespace Hyperbc\PaymentGateway\Model\Adminhtml\Source;

use Magento\Framework\UrlInterface;

class CallbackUrlComment implements \Magento\Config\Model\Config\CommentInterface
{
    protected $urlInterface;

    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    public function getCommentText($elementValue)
    {
        $webhook = $this->urlInterface->getBaseUrl() . 'rest/all/V1/hyperbc/order/status/update';
        $pointOne = __('1. Contact Hypberbc support to register this callback value (%1)', $webhook);
        // $pointTwo = __('2. Then go to <a href="https://business.hyperbc.me/app/settings/api" target="_blank"> the Settings -&gt; API page </a> and save %1 in the Callback URL field', $webhook);
        // return "$pointOne <br/> $pointTwo";
        return "$pointOne";
    }
}
