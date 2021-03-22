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
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $customerSession = $objectManager->create('Magento\Customer\Model\Session');
    $pontosUsados = $customerSession->getPontosUsados();
    $output['quoteData']['pontos-usados'] = $pontosUsados;

    return $output;
  }
}
?>
