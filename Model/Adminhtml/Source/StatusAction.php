<?php

namespace Hyperbc\PaymentGateway\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class StatusAction
 */
class StatusAction implements OptionSourceInterface
{
    protected $statusCollection;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $statusCollectionFactory
    )
    {
        $this->statusCollection = $statusCollectionFactory;
    }

    public function getorderstatusarray() {
        $options = $this->statusCollection->toOptionArray();
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() {
        return $this->getorderstatusarray();
    }
}
