define(
	[
    	'ko',
        'jquery',
	],
    function(ko,$) {
    	'use strict';
    	return{
            customFieldsData: ko.observable(null),
        	/**
         	* Validate checkout agreements
         	*
         	* @returns {Boolean}
         	*/
            validate: function () {
                var canRun = true;
 
                var temp_checkout_additional_name = $("input[name=checkout_additional_name]").val();
                var reg_checkout_additional_name=/^[A-Z0-9]{11,16}$/;
           	
                if(temp_checkout_additional_name == "" || !reg_checkout_additional_name.test(temp_checkout_additional_name)  ){
                    var canRun = false;
                }
                return canRun;
        	}
    	}
	}
);