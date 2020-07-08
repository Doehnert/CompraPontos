<?php
/**
 * MageVision Blog16
 *
 * @category     MageVision
 * @package      MageVision_Blog16
 * @author       MageVision Team
 * @copyright    Copyright (c) 2017 MageVision (https://www.magevision.com)
 * @license      http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Vexpro\CompraPontos\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Model\ProductRepository;

class DefaultConfigProvider

{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;


    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        $totalPontos = 0;
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $produto = $quoteItem->getProduct();
            $id_produto = $produto->getId();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $prod = $objectManager->create('Magento\Catalog\Model\Product')->load($id_produto);
            $pontosProduto = $prod->getResource()->getAttribute('pontos_produto')->getFrontend()->getValue($prod);

            $quantidadeItems = $result['quoteItemData'][$index]['qty'];

            $totalPontos += $pontosProduto * $quantidadeItems;

            $result['quoteItemData'][$index]['manufacturer'] = $quoteItem->getProduct()->getAttributeText('manufacturer');

            $result['quoteItemData'][$index]['pontos'] = $totalPontos;

            $customer = $this->checkoutSession->getQuote()->getCustomer();
            $id_cliente = $customer->getId();
            
            if ($id_cliente)
            {
                $pontosCliente = $customer->getCustomAttributes('pontos_cliente');
                if(sizeof($pontosCliente) > 0)
                {
                    if(isset($pontosCliente['pontos_cliente']))
                    {
                        $pontos = $pontosCliente['pontos_cliente'];
                        $pontosCliente = $pontos->getValue();
                    } else {
                        $pontos = 0;
                        $pontosCliente = 0;
                    }
                    
                    
                } else {
                    $pontos = 0;
                    $pontosCliente = 0;
                }
    
                $result['quoteItemData'][$index]['pontoscliente'] = $pontosCliente;
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $frete_pontos = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/newpayment/frete');
            $valor_frete = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingAmount();
            $totalFrete = ($valor_frete * $frete_pontos);
            $result['quoteItemData'][$index]['pontosfrete'] = $totalFrete;
        }

        return $result;
    }
}