define([
	'Magento_Checkout/js/view/summary/abstract-total',
	'Magento_Checkout/js/model/quote',
	'Magento_Catalog/js/price-utils',
	'Magento_Checkout/js/model/totals',
	'Magento_Customer/js/customer-data',
], function (Component, quote, priceUtils, totals, customerData) {
	'use strict';

	return Component.extend({
		defaults: {
			template: 'Vexpro_CompraPontos/summary/desconto',
		},

		/**
		 * @return {*}
		 */
		isDisplayed: function () {
			return this.isFullMode();
		},

		/**
		 * Get pure value.
		 */
		getPureValue: function () {
			console.log(totals);
			var totals = quote.getTotals()();
			console.log(quote);
			console.log(totals);

			if (totals) {
				return quote.getTotals()()['total_segments'][2]['value'];
			}
			return quote['grand_total'];
			//return window.checkoutConfig['descontado'];
		},

		/**
		 * @return {*|String}
		 */
		getValue: function () {
			//return this.getFormattedPrice(this.getPureValue());
			var priceFormat = {
				decimalSymbol: '.',
				groupLength: 3,
				groupSymbol: ',',
				integerRequired: false,
				pattern: '$%s',
				precision: 2,
				requiredPrecision: 2,
			};

			return priceUtils.formatPrice(-this.getPureValue(), priceFormat, true);
		},
	});
});
