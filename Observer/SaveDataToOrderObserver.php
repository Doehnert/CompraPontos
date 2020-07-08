<?php

namespace Vexpro\CompraPontos\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveDataToOrderObserver implements ObserverInterface
{
    public function execute(EventObserver $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');        
        $totalUsado = $customerSession->getPontosUsados();

        $order = $observer->getOrder();
        $order->setPontosUsados($totalUsado);

        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        
        return $this;
    }
}