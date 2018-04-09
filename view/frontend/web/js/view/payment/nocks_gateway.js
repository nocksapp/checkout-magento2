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
                type: 'nocks_gateway',
                component: 'Magento_NocksPaymentGateway/js/view/payment/method-renderer/nocks_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
