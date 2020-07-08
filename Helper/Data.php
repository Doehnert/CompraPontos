<?php
namespace Vexpro\CompraPontos\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
 
class Data extends AbstractHelper
{
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Product $product, \Magento\Framework\Data\Form\FormKey $formkey, \Magento\Quote\Model\QuoteFactory $quote, \Magento\Quote\Model\QuoteManagement $quoteManagement, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Sales\Model\Service\OrderService $orderService)
    {
        $this->storeManager = $storeManager;
        $this->product = $product;
        $this->formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        parent::__construct($context);
    }
 
    public function createOrder($order)
    {
        $store = $this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($order['email']); // load customet by email address
       if (!$customer->getEntityId()) {
            //If not avilable then create this customer
           $customer->setWebsiteId($websiteId)->setStore($store)->setFirstname($order['shipping_address']['firstname'])->setLastname($order['shipping_address']['lastname'])->setEmail($order['email'])->setPassword($order['email']);
            $customer->save();
        }
        $quote = $this->quote->create(); // Create Quote Object
       $quote->setStore($store); // Set Store
       $customer = $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); // Assign quote to Customer
 
        //add items in quote
       foreach ($order['items'] as $item) {
            $product = $this->product->load($item['product_id']);
            $product->setPrice($item['price']);
            $quote->addProduct($product, intval($item['qty']));
        }
 
        $quote->getBillingAddress()->addData($order['shipping_address']);
        $quote->getShippingAddress()->addData($order['shipping_address']);
 
        // Collect Rates and Set Shipping & Payment Method
 
       $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()->setShippingMethod('freeshipping_freeshipping');
        $quote->setPaymentMethod('checkmo');
        $quote->setInventoryProcessed(false);
        $quote->save();
 
        // Set Sales Order Payment
       $quote->getPayment()->importData(['method' => 'checkmo']);
 
        // Collect Totals & Save Quote
       $quote->collectTotals()->save();
 
        // Create Order From Quote
       $orderdata = $this->quoteManagement->submit($quote);
 
        $orderdata->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if ($orderdata->getEntityId()) {
            $result['order_id'] = $orderdata->getRealOrderId();
        } else {
            $result = ['error' => 1, 'msg' => 'Your custom message'];
        }
        return $result;
    }
}
?>