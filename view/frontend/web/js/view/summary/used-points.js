define([
	'Magento_Checkout/js/view/summary/abstract-total',
	'Magento_Checkout/js/model/quote',
	'Magento_Catalog/js/price-utils',
	'Magento_Checkout/js/model/totals',
	'Magento_Customer/js/customer-data',
], function (Component, quote, priceUtils, totals, customerData) {
	'use strict';
	var quoteItemData = window.checkoutConfig.quoteItemData;

	return Component.extend({
		defaults: {
			template: 'Vexpro_CompraPontos/summary/used-points',
		},

		/**
		 * @return {*}
		 */
		isDisplayed: function () {
			return this.isFullMode();
		},

		getUsedPoints: function () {
			return window.checkoutConfig['usados'];
		},
	});
});
