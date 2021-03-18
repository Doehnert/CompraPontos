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
                $item->setAdditionalData('regate');
                //$item->setDiscountPercent(100);
                //$item->setCustomPrice($finalprice);
                //$item->setOriginalCustomPrice($finalprice);
                $item->getProduct()->setIsSuperMode(true);
                $item->saveItemOptions();
                break;
            }
        }
        $this->cart->save();
    }
}
