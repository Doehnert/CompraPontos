<?php

use Magento\Framework\Controller\ResultFactory;


namespace Vexpro\CompraPontos\Controller\Pontos;

class Pagamentos extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    protected $cacheManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \Magento\Framework\App\Cache\Manager $cacheManager
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->cacheManager = $cacheManager;
        return parent::__construct($context);
    }

    private function whereYouNeedToCleanCache()
    {
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
    
        // or this
        $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customer = $customerSession->getCustomer();
        $customerId = $customer->getId();
        $pontosCliente = $customerSession->getPontosCliente();
        
        // Instancia o carrinho e carrega o preço total sendo cobrado
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote = $cart->getQuote();

        $grandTotal = $quote->getGrandTotal();

        if ($grandTotal == 0){
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/cashondelivery/active', 0);
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/banktransfer/active', 0);
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/checkmo/active', 0);

            $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->save('payment/newpayment/active', 1);

            $this->whereYouNeedToCleanCache();
        } else {
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/cashondelivery/active', 1);
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/banktransfer/active', 1);
            $objectManager->get('Magento\Framework\App\Config\Storage\WriterInterface')->save('payment/checkmo/active', 1);

            $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->save('payment/newpayment/active', 0);

            $this->whereYouNeedToCleanCache();
        }
    }
}
