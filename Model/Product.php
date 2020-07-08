<?php

	namespace Vexpro\CompraPontos\Model;

    class Product
	{
		// public function afterGetName(\Magento\Catalog\Model\Product $subject, $result) {
		// 	return "Apple ".$result; // Adding Apple in product name
        // }

    	public function afterGetPrice(
				\Magento\Catalog\Model\Product $product,
				$result
			) {
            // Se o usuário for comprar com pontos então muda o preço para 0
            if (1==1)
            {
				$pontosProduto = $product->getResource()->getAttribute('pontos_produto')->getFrontend()->getValue($product);
				
				$result = $result;


				// $om = \Magento\Framework\App\ObjectManager::getInstance();
				// $customerSession = $om->get('Magento\Customer\Model\Session');
				// $customerData = $customerSession->getCustomer()->getData(); //get all data of customerData
				// $idCustomer = $customerSession->getCustomer()->getId();//get id of customer

				// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				// $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
				// $customer = $customerRepository->getById($idCustomer);
				// $cattrValue = $customer->getCustomAttribute('pontos_cliente');
				
				
                //$result = 0;
            }
            
			return $result;
		}

	}