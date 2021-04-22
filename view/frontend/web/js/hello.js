define(['jquery', 'keyboard'], function ($) {
	'use sctrict';

    $(document).ready(function () {

		$('.cryxpad-container').cryxpad({
			inputFormId: 'senha',
			removeButtonId: 'cryxpad-remove-btn',
			// validateButtonId: 'cryxpad-validate-btn',
			carreaux: 5, // nombre de carreaux sur une ligne du clavier
			width: 50, // longeur d'un bouton
			height: 50, // hauteur d'un bouton
			/*'buttonClass':"btn btn-primary",*/
		});

		$('.cryxpad-container').hide();

		$('#icon').click(function () {
			$('.cryxpad-container').toggle();
		});
	});
});
