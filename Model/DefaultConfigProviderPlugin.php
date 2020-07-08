<?php
namespace Vexpro\CompraPontos\Model;
use Magento\Checkout\Model\Session as CheckoutSession;
class DefaultConfigProviderPlugin
{
  /**
   * @var CheckoutSession
   */
  private $checkoutSession;
  /**
   * @var \Magento\Quote\Api\CartRepositoryInterface
   */
  private $quoteRepository;
    public function __construct(
      CheckoutSession $checkoutSession,
      \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
  ){
    $this->checkoutSession = $checkoutSession;
    $this->quoteRepository = $quoteRepository;
  }
  public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $config,
  $output){
    $output= $this->getCustomQuoteData($output);
    return $output;
  }
  private function getCustomQuoteData($output)
  {
      if ($this->checkoutSession->getQuote()->getId()) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $frete_pontos = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/newpayment/frete');
        $valor_frete = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingAmount();
        $totalFrete = ($valor_frete * $frete_pontos);
        $output['quoteData']['pontosfrete'] = $totalFrete;
      }
      return $output;
  }
}
?>