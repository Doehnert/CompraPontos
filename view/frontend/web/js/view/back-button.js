define([
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator'
], function (Component, stepNavigator) {
    return Component.extend({
        goToPrevStep: function () {
            stepNavigator.navigateTo('shipping');
        }
    })
});