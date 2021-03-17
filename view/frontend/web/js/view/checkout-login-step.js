/*browser:true*/
/*global define*/
define([
	'ko',
	'uiComponent',
	'underscore',
	'Magento_Checkout/js/model/step-navigator',
	'Magento_Customer/js/model/customer',
	'jquery',
	'Magento_Checkout/js/model/cart/cache',
	'Magento_Checkout/js/action/get-totals',
	'Magento_Customer/js/customer-data',
	'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/cart/totals-processor/default',
	'Magento_Checkout/js/model/totals',
	'jquery',
], function (
	ko,
	Component,
	_,
	stepNavigator,
	customer,
	defaultTotal,
	cartCache,
	getTotalsAction,
	customerData,
	quote,
	totalsDefaultProvider,
	totals,
	$,
) {
	'use strict';
	/**
	 * check-login - is the name of the component's .html template
	 */

	return Component.extend({
		defaults: {
			template: 'Vexpro_CompraPontos/check-login',
		},

		isVisible: ko.observable(true),
		isLogedIn: customer.isLoggedIn(),
		stepCode: 'isLogedCheck',

		pontosCliente: ko.observable(
			customer.customerData.custom_attributes.pontos_cliente.value,
		),
		pontosUsados: ko.observable(0),
		mensagem: ko.observable(''),

		//step title value
		stepTitle: ko.observable('Deseja usar os pontos?'),
		precoTotal: ko.observable(0),
		fim: ko.observable(false),

		/**
		 *
		 * @returns {*}
		 */
		initialize: function () {
			this._super();
			console.log(customer.customerData.custom_attributes.pontos_cliente.value);

			jQuery.get('/comprapontos/pontos/sessao', '', function (data) {
				totalsDefaultProvider.estimateTotals(quote.shippingAddress());
				totalsDefaultProvider.estimateTotals();
			});
			// register your step
			stepNavigator.registerStep(
				this.stepCode,
				//step alias
				null,
				this.stepTitle,
				//observable property with logic when display step or hide step
				this.isVisible,

				_.bind(this.navigate, this),

				/**
				 * sort order value
				 * 'sort order value' < 10: step displays before shipping step;
				 * 10 < 'sort order value' < 20 : step displays between shipping and payment step
				 * 'sort order value' > 20 : step displays after payment step
				 */
				15,
			);

			this.produtos = ko.computed(function () {
				var produtos = [];

				for (let i = 0; i < quote.getItems().length; i++) {
					let quantity = quote.getItems()[i].qty;
					let nome = quote.getItems()[i].name;
					let pontos = quote.getItems()[i].pontos;

					pontos = pontos / quantity;
					if (pontos == 0) continue;
					let img = quote.getItems()[i].thumbnail;
					let desabilitado = false;
					let preco = quote.getItems()[i].price;
					let id = quote.getItems()[i].item_id;

					for (let count = 0; count < quantity; count++) {
						if (this.pontosCliente() < pontos) {
							desabilitado = true;
						}
						if (pontos == 0) {
							desabilitado = true;
						}

						produtos.push({
							nome: nome,
							pontos: pontos,
							usaPontos: ko.observable(false),
							img: img,
							desabilitado: ko.observable(desabilitado),
							preco: preco,
							id: id,
						});
					}
				}
				return produtos;
			}, this);
			console.log('qtd de produtos = ' + this.produtos.length);
			if (this.produtos.length == 0) {
				console.log($('#avancar'));
				//$('#avancar').first().trigger('click');
				// this.fim(true);
				//this.navigateToNextStep();
				// setTimeout(function(){
				//     $("#avancar").click();
				// },1000);
			}

			// this.produtos[0].usaPontos = true;

			this.pontosAtuais = ko.computed(function () {
				if (this.fim() == false) {
					var pontosCliente = Number(this.pontosCliente());
					let preco_total = totals.getSegment('grand_total').value;
					this.precoTotal(preco_total);

					for (var i = 0; i < this.produtos().length; i++) {
						if (pontosCliente < this.produtos()[i].pontos) {
							this.produtos()[i].desabilitado(true);
						} else {
							this.produtos()[i].desabilitado(false);
						}
					}

					for (var i = 0; i < this.produtos().length; i++) {
						var pontosUsados = Number(this.produtos()[i].pontos);

						if (this.produtos()[i].usaPontos() == true) {
							if (pontosCliente - pontosUsados < 0) {
								alert('Você não possui pontos suficientes sdf!');
								this.produtos()[i].desabilitado(true);
								this.produtos()[i].usaPontos(false);
							} else {
								pontosCliente -= pontosUsados;
								this.precoTotal(
									Number(this.precoTotal()) - Number(this.produtos()[i].preco),
								);

								for (var j = 0; j < this.produtos().length; j++) {
									if (this.produtos()[j].usaPontos() == false) {
										if (Number(this.produtos()[j].pontos) > pontosCliente) {
											this.produtos()[j].desabilitado(true);
											this.produtos()[j].usaPontos(false);
										} else {
											this.produtos()[j].desabilitado(false);
										}
									}
								}
							}
						}
					}
					var x = pontosCliente.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
					return x;
				}
			}, this);
			return this;
		},

		/**
		 * The navigate() method is responsible for navigation between checkout step
		 * during checkout. You can add custom logic, for example some conditions
		 * for switching to your custom step
		 */
		navigate: function () {},

		/**
		 * @returns void
		 */
		navigateToNextStep: function () {
			if (this.fim() == false) {
				let preco = 0;
				let pontos = 0;
				for (var i = 0; i < this.produtos().length; i++) {
					console.log(this.produtos()[i].id);
					if (this.produtos()[i].usaPontos() == true) {
						// jQuery.ajax({
						// 	url: '/comprapontos/pontos/retiraprod',
						// 	type: 'POST',
						// 	async: false,
						// 	data: {
						// 		prod_id: this.produtos()[i].id,
						// 	},
						// });

						pontos += Number(this.produtos()[i].pontos);
						preco += Number(this.produtos()[i].preco);
					}
				}

				jQuery.ajax({
					url: '/comprapontos/pontos/retira',
					type: 'POST',
					async: false,
					data: {
						pontos: pontos,
						preco: preco,
					},
					success: function (response) {},
				});

				totalsDefaultProvider.estimateTotals(quote.shippingAddress());
				totalsDefaultProvider.estimateTotals();
			}

			stepNavigator.next();
			this.fim(true);

			let quantity = 0;
			for (let i = 0; i < quote.getItems().length; i++) {
				quantity += quote.getItems()[i].qty;
			}
			for (let i = 0; i < this.produtos().length; i++) {
				this.produtos()[i].desabilitado(true);
			}
		},
	});
});
