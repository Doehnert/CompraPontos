<?php

namespace Vexpro\CompraPontos\Controller\Pontos;

use Magento\Framework\Controller\ResultFactory;

class Retiraprod extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    // protected $_pageFactory;
    // protected $cacheManager;
    // protected $quote;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Checkout\Model\Cart $cart
    )
    {
        $this->cart = $cart;
        return parent::__construct($context);
    }

    /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuoteData()
    {
        $this->_checkoutSession->getQuote();
        if (!$this->hasData('quote')) {
            $this->setData('quote', $this->_checkoutSession->getQuote());
        }
        return $this->_getData('quote');
    }

     /**
     * Create Order On Your Store
     *
     * @param array $orderData
     * @return array
     *
    */
    private function whereYouNeedToCleanCache()
    {
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Recebe os pontos acumulados e os pontos enviados por último
        $post = $this->getRequest()->getPostValue();
        $orderItemId = $post['prod_id'];
        $items = $this->cart->getQuote()->getAllItems();
        foreach($items as $item)
        {
            if ($item->getItemId() == $orderItemId)
            {
                //$subprice = $item->getPrice();
                $finalprice = 0;
                $item->setCustomPrice($finalprice);
                $item->setOriginalCustomPrice($finalprice);
                $item->getProduct()->setIsSuperMode(true);
                $item->saveItemOptions();
                break;
            }
        }
        $this->cart->save();

    //     $preco = $post['preco'];

    //     // Instancia o cliente e carrega sua pontuação
    // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    // //     $customerSession = $objectManager->create('Magento\Customer\Model\Session');
    // //     $customer = $customerSession->getCustomer();
    // //     $customerId = $customer->getId();
    // //     $pontosCliente = $customerSession->getPontosCliente();

    // //     // Instancia o carrinho e carrega o preço total sendo cobrado
    // $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

    // $quote = $cart->getQuote();
    // $grandTotal = $quote->getGrandTotal();

    //     // Retira o preço dos produtos selecionados do preço total a ser pago
    //     $quote->setGrandTotal($grandTotal - $preco);
    //     $grandTotal = $quote->getGrandTotal();
    //     $quoteId = $quote->getId();
    //     $quote->save();

    //     // Subtrai dos pontos do cliente os pontos desse produto
    //     $customerSession->setPontosCliente($pontosCliente - $pontos);
    //     $pontosCliente = $customerSession->getPontosCliente();
    //     $val = $preco + $customerSession->getDesconto();
    //     $customerSession->setDesconto($val);
    //     $customerSession->setPontosUsados($pontos);
    }
}
