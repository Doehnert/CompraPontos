define(
    [
        'Stackoverflow_Toan/js/view/checkout/summary/mycustomstuff'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);