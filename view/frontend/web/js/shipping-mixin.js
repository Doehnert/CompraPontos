define([
    '../model/quote',
    'Magento_Checkout/js/model/cart/totals-processor/default'
], function (quote, totalsDefaultProvider) {
    'use strict';

    return function (shippingMethod) {
        console.log("select shipping methods");
        quote.shippingMethod(shippingMethod);
        totalsDefaultProvider.estimateTotals(quote.shippingAddress());

    };
});