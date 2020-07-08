<?php
	
	namespace Vexpro\CompraPontos\Model;

	class Cart
	{
		public function beforeAddProduct(
			\Magento\Checkout\Model\Cart $subject,
			$productInfo,
			$requestInfo = null
		) {
			$requestInfo['qty'] = 1; // increasing quantity to 10
			return array($productInfo, $requestInfo);
		}
	}