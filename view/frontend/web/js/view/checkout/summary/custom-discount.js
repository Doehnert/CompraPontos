define(
    [
       'jquery',
       'Magento_Checkout/js/view/summary/abstract-total',
       'Magento_Checkout/js/model/quote',
       'Magento_Checkout/js/model/totals',
       'Magento_Catalog/js/price-utils',
       'Magento_Checkout/js/model/cart/totals-processor/default',
       'Magento_Checkout/js/model/totals'
    ],
    function ($,Component,quote,totals,priceUtils,totalsDefaultProvider) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Vexpro_CompraPontos/checkout/summary/custom-discount'
            },
            totals: quote.getTotals(),

            precoTotal: ko.observable(0),
            
            isDisplayedCustomdiscountTotal : function () {
                return true;
            },
            getCustomdiscountTotal : function (price) {
                var price = this.precoTotal();
                return this.getFormattedPrice(price);
                
            }
         });
    }
);