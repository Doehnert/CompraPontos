<?php

use Magento\Framework\Controller\ResultFactory;


namespace Vexpro\CompraPontos\Controller\Pontos;

class Store extends \Magento\Framework\App\Action\Action
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
        $pontosUsados = $post['acumulados'];
        $descontoTotal = $post['pontos'];

        // Instancia o cliente e carrega sua pontuação
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customer = $customerSession->getCustomer();
        $customerId = $customer->getId();
        $pontosCliente = $customer->getPontosCliente();

        // Instancia o carrinho e carrega o preço total sendo cobrado
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote = $cart->getQuote();
        $grandTotal = $quote->getGrandTotal();


        // Converte os pontos acumulados até o momento com um desconto em dinheiro
        // TODO: fazer a conversão conforme a necessidade
        $desconto = $pontosUsados;
        $customerSession->setDesconto($desconto);
        $customerSession->setPontosUsados($pontosUsados);

        echo "grandTotal = " . $grandTotal;
        echo "\n";
        echo "desconto = " . $desconto;
        echo "\n";

        $items = $quote->getItemsCollection();
        foreach($items as $item)
        {
            $preco = $item->getPrice();
            echo "preço do item " . $item->getName() . " = ";
            echo $preco;
            echo "\n";
        }

        echo "Desconto = " . $desconto;
        echo "\n";
        $grandTotal = $quote->getGrandTotal();
        $quote->setGrandTotal($grandTotal - $descontoTotal);
        $grandTotal = $quote->getGrandTotal();

        echo "grandTotal = " . $grandTotal;
        echo "\n";


        // o que fazer se o valor final for 0
        if($grandTotal == 0)
        {
            // //$quote->setSubTotal(0);
            // $items = $quote->getItemsCollection();
            // foreach($items as $item)
            // {
            //     echo "setando preco em 0";
            //     echo "\n";
            //     $customPrice = 0;
            //     $item->setCustomPrice($customPrice);
            //     $item->setOriginalCustomPrice($customPrice);
            //     $item->getProduct()->setIsSuperMode(true);
            // }
            // $customerSession->setDesconto(0);
            // $quote->setGrandTotal(1);
            // $quote->setSubTotal(1);
            // $grandTotal = $quote->getGrandTotal();
            // $cart->save();
            // echo "grandTotal = " . $grandTotal;
            // echo "\n";
        }


        // This will return the current quote
        $quoteId = $quote->getId();
        $quote->save();
        exit;
    }
}
