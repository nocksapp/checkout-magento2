define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_NocksPaymentGateway/payment/form'
            },
            redirectAfterPlaceOrder: false,

            getCode: function() {
                return 'nocks_gateway';
            },

            getData: function() {
                return {
                    'method': this.item.method
                };
            },

            afterPlaceOrder: function() {
                window.location = url.build('nocks/startpayment/');
            }
        });
    }
);