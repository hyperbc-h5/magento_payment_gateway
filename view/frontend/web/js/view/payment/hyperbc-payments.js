define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'hyperbc_paymentgateway',
                component: 'Hyperbc_PaymentGateway/js/view/payment/method-renderer/hyperbc-method'
            }
        );
        return Component.extend({});
    }
);
