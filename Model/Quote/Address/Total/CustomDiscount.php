<?php

namespace Vexpro\CompraPontos\Model\Quote\Address\Total;

class CustomDiscount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    public $desconto;

    public $conta;

    /**
    * @var \Magento\Framework\Pricing\PriceCurrencyInterface
    */
    protected $_priceCurrency;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency [description]
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->conta = 0;

    }

    public function setDesconto($desconto)
    {
        $this->desconto = $desconto;
    }



    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
            parent::collect($quote, $shippingAssignment, $total);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            if($customerSession && $this->conta<1)
            {
                $this->desconto = (float)$customerSession->getDesconto();
                $customDiscount = -$this->desconto;

                $total->addTotalAmount('customdiscount', $customDiscount);
                $total->addBaseTotalAmount('customdiscount', $customDiscount);
    
                $quote->setCustomDiscount($customDiscount);
                $this->conta++;
            } else {
                $this->desconto = (float)$customerSession->getDesconto();
                $customDiscount = -$this->desconto;

                $total->addTotalAmount('customdiscount', $customDiscount);
   
                $quote->setCustomDiscount($customDiscount);
                $this->conta++;
            }

            return $this;
        }
    
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code' => 'Custom_Discount',
            'title' => $this->getLabel(),
            'value' => $this->desconto,
        ];
    }

    /**
     * get label
     * @return string
     */
    public function getLabel()
    {
        return __('Custom Discount');
    }
}