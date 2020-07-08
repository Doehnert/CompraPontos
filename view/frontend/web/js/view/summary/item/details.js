/**
 * MageVision Blog16
 *
 * @category     MageVision
 * @package      MageVision_Blog16
 * @author       MageVision Team
 * @copyright    Copyright (c) 2017 MageVision (https://www.magevision.com)
 * @license      http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'uiComponent',
        'ko',
        'jquery',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'Magento_Checkout/js/model/cart/cache',

        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/shipping-rate-registry'
    ],
    function (Component) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        return Component.extend({
            defaults: {
                template: 'Vexpro_CompraPontos/summary/item/details'
            },
            quoteItemData: quoteItemData,
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            getManufacturer: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                return item.manufacturer;
            },
            getPontos: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                return item.pontos;
            },
            getPontosCliente: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                return item.pontoscliente;
            },
            getPontosFrete: function(quoteItem) {
                var item = this.getItem(quoteItem.item_id);
                return item.pontosfrete;
            },
            getItem: function(item_id) {
                var itemElement = null;
                _.each(this.quoteItemData, function(element, index) {
                    if (element.item_id == item_id) {
                        itemElement = element;
                    }
                });
                return itemElement;
            },

            
        });
    },
    function (quote, defaultProcessor, customerAddressProcessor, rateRegistry) {
        'use strict';
 
        var processors = [];
 
        rateRegistry.set(quote.shippingAddress().getCacheKey(), null);
 
        processors.default =  defaultProcessor;
        processors['customer-address'] = customerAddressProcessor;
 
        var type = quote.shippingAddress().getType();
 
        if (processors[type]) {
           processors[type].getRates(quote.shippingAddress());
        } else {
           processors.default.getRates(quote.shippingAddress());
        }
 
     },
);