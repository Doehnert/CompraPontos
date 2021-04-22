var config = {
	urlArgs: 'bust=' + new Date().getTime(),
	map: {
		'*': {
			'Magento_Checkout/template/sidebar.html':
                'Vexpro_CompraPontos/template/sidebar.phtml',
            keyboard: 'Vexpro_CompraPontos/js/jquery.cryxpad',
            hello: 'Vexpro_CompraPontos/js/hello',
		},
	},
};
