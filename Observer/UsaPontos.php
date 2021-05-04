<?php
namespace Vexpro\CompraPontos\Observer;

class UsaPontos implements \Magento\Framework\Event\ObserverInterface
{
    protected $cacheManager;

    public function __construct(
        \Magento\Framework\App\Cache\Manager $cacheManager
     )
     {
         $this->cacheManager = $cacheManager;
     }

     private function whereYouNeedToCleanCache()
     {
         $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
         $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
     }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        // Carrega a variável de sessão desconto que possui o valor do desconto em R$
        $quote = $observer->getQuote();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $desconto = $customerSession->getDesconto();

        $pontosCliente = $customerSession->getPontosCliente();

        // Carrega informações do usuário logado
        $customerRepository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
        $customer = $quote->getCustomer();
        $id_cliente = $customer->getId();
        $customer = $customerRepository->getById($id_cliente);

        // Total da compra
        $grandTotal = $quote->getGrandTotal();
        // Aplica o desconto dos pontos
        //$quote->setGrandTotal($grandTotal-$desconto);

        // COMENTADO PARA NAO ALTERAR PONTOS CLIENTE POR ENQUANTO
        //$customer->setCustomAttribute('pontos_cliente',$pontosCliente);
        //$customerRepository->save($customer);

        // Depois da compra volta a zerar o desconto
        // $customerSession->unsDesconto();
        //$customerSession->unsPontosCliente();

        //TODO: limpar o cache
        //$this->whereYouNeedToCleanCache();
    }
}
