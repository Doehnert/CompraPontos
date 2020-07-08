<?php

namespace Vexpro\CompraPontos\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;


class PaymentMethodAvailable implements ObserverInterface
{

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
    }


    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // TODO: desabilitar metodos de pagamento caso o valor a ser pago seja zero
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cartObj = $objectManager->get('\Magento\Checkout\Model\Cart');
        $subTotal = $cartObj->getQuote()->getSubtotal(); //Current Cart Subtotal
        $grandTotal = $cartObj->getQuote()->getGrandTotal(); //Cart Grand total        

        if ($grandTotal == 0)
        {
            $quote = $cartObj->getQuote();
            $quote->setGrandTotal(100);
            $quote->setSubtotal(10);
            $quote->collectTotals();
            $quote->save();
        }
    }
}