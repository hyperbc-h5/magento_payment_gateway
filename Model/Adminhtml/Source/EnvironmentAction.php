<?php

namespace Hyperbc\PaymentGateway\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class EnvironmentAction
 */
class EnvironmentAction implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'Sandbox',
                'label' => 'Sandbox'
            ],
            [
                'value' => 'Production',
                'label' => 'Production'
            ]
        ];
    }
}
