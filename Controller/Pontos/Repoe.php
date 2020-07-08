<?php

use Magento\Framework\Controller\ResultFactory;


namespace Vexpro\CompraPontos\Controller\Pontos;

class Repoe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        echo "controller store";
        echo "\n";
        // Recebe os pontos acumulados e os pontos enviados por último
        $post = $this->getRequest()->getPostValue();
        $pontos = $post['pontos'];

        // Instancia o cliente e carrega sua pontuação
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customer = $customerSession->getCustomer();
        $customerId = $customer->getId();
        $pontosCliente = $customerSession->getPontosCliente();
        
        // Instancia o carrinho e carrega o preço total sendo cobrado
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote = $cart->getQuote();
        $grandTotal = $quote->getGrandTotal();

        //$pontosCliente = $customerSession->getPontosUsados();
        $customerSession->setPontosCliente($pontosCliente + $pontos);
        $pontosCliente = $customerSession->getPontosCliente();
        echo "Novo valor na sessao = " . ($pontosCliente);
    }
}
