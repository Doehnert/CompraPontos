<?php

	namespace Vexpro\CompraPontos\Model;

    class Product
	{
    	public function afterGetPrice(
				\Magento\Catalog\Model\Product $product,
				$result
			) {
			$pontosProduto = $product->getResource()->getAttribute('pontos_produto')->getFrontend()->getValue($product);
            
			return $result;
		}

	}