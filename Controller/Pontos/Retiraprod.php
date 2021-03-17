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
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Catalog\Model\ProductFactory $productFactory,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \Magento\Framework\App\Cache\Manager $cacheManager,
       \Magento\Quote\Model\QuoteManagement $quoteManagement,
       \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Catalog\Model\Product $product
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->cacheManager = $cacheManager;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->shippingRate = $shippingRate;
        $this->quote = $quote;
        $this->_product = $product;
        return parent::__construct($context);
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
        $prod_id = $post['prod_id'];
    //     $preco = $post['preco'];

    //     // Instancia o cliente e carrega sua pontuação
    //     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    //     $customerSession = $objectManager->create('Magento\Customer\Model\Session');
    //     $customer = $customerSession->getCustomer();
    //     $customerId = $customer->getId();
    //     $pontosCliente = $customerSession->getPontosCliente();

    //     // Instancia o carrinho e carrega o preço total sendo cobrado
    //     $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
    //     $quote = $cart->getQuote();
    //     $grandTotal = $quote->getGrandTotal();

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
