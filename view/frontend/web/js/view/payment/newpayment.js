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
                 type: 'newpayment',
                 component: 'Vexpro_CompraPontos/js/view/payment/method-renderer/newpayment-method'
             }
         );
         /** Add view logic here if needed */
         return Component.extend({
         
        defaults: {
            template: 'Vexpro_CompraPontos/checkout/foolsample'
        },
        /**
        * Init component
        */
       initialize: function () {
        this._super();
        this.config = window.checkoutConfig.foolsample;
        },
        
        getTitle: function () {
            return this.title;
        },


     });
    }
    );