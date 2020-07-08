define(
    [
        'ko',
        'uiComponent'
    ],
    function (ko, Component) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Mageplaza_HelloWorld/newsletter'
            },
            isRegisterNewsletter: true
        });
    }
);