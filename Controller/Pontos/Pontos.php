<?php

use Magento\Framework\Controller\ResultFactory;


namespace Vexpro\CompraPontos\Controller\Pontos;

class Pontos extends \Magento\Framework\App\Action\Action
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $quote = $cart->getQuote();

        $items = $quote->getItemsCollection();
        $totalPontos = 0;
        foreach($items as $item)
        {
            $preco = $item->getPrice();
            //$pontos = $item->getResource()->getAttribute('pontos_produto')->getFrontend()->getValue($product);
            $totalPontos += $pontos;
            echo "preço do item " . $item->getName() . " = ";
            echo $preco;
            echo "\n";
        }

        return $totalPontos;
    }
}
