/*global define*/
define([
	'knockout',
	'jquery',
	'mage/url',
    'Magento_Ui/js/form/form',
    'Bodak_CheckoutCustomForm/js/model/checkout/custom-checkout-form',
	'Magento_Checkout/js/model/payment/additional-validators'
], function(ko, $, urlFormatter, Component, customer, quote, urlBuilder, errorProcessor, cartCache, formData, additionalValidators) {
	'use strict';
	var isCustomer = customer.isLoggedIn();
    additionalValidators.registerValidator(formData);
 
	});
 
});