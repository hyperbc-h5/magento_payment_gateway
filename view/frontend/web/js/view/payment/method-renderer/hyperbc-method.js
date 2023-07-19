define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, Component, url, customerData, errorProcessor, fullScreenLoader) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Hyperbc_PaymentGateway/payment/hyperbc-form'
            },
            getCode: function() {
                return 'hyperbc_paymentgateway';
            },
            isActive: function() {
                return true;
            },
            afterPlaceOrder: function () {
                var custom_controller_url = url.build('hyperbc/payment/create');

                $.post(custom_controller_url, 'json')
                    .done(function (response) {
                        window.location.href = response.redirectUrl;
                    })
                    .fail(function (response) {
                        $.post(url.build('hyperbc/payment/cancel'), 'json')
                        errorProcessor.process(response, this.messageContainer);
                    })
                    .always(function () {
                        fullScreenLoader.stopLoader();
                    });
            },
            getHyperbcMessage: function () {
                return window.checkoutConfig.payment.hyperbc_message[this.item.method];
            }
        });
    }
);
