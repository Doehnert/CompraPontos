<?php

	namespace Vexpro\CompraPontos\Model;

	class Order
	{
		public function beforePlace(
			Magento\Sales\Api\OrderManagementInterface $order,
				$result
			) {
			$result = 0;
			return $result;
		}

	}