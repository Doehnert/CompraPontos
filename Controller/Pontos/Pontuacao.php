<?php
namespace Vexpro\CompraPontos\Controller\Pontos;

class Pontuacao extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
       \Magento\Checkout\Model\Cart $cart
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
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
        $result = $this->resultJsonFactory->create();

        $items = $this->cart->getQuote()->getAllItems();
        $pontosUsados = 0;
        foreach($items as $item)
        {
            if ($item->getAdditionalData() === "regate")
            {
                $productData = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                $pontosUsados += (int) $productData->getPontosProduto();
            }
        }

        return $result->setData(['pontosUsados' => $pontosUsados]);
    }
}
