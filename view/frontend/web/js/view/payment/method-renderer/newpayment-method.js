define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
            template: 'Vexpro_CompraPontos/payment/newpayment'
        },
    /** Returns send check to info */
        getMailingAddress: function() {
            return window.checkoutConfig.payment.checkmo.mailingAddress;
        },
    });
    }
);