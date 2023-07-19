<?php

namespace Hyperbc\PaymentGateway\Model\Adminhtml\Source;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

/**
 * Class DisabledField
 */
class DisabledField extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $urlBuilder;

    /**
     * @param Context $context
     * @param array $data
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setDisabled('disabled');

        $webhook = $this->urlBuilder->getBaseUrl() . 'rest/all/V1/order/status/update';
        $element->setValue($webhook);

        return $element->getElementHtml();
    }
}
