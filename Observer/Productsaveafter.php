<?php
namespace Vexpro\CompraPontos\Observer;

use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Productsaveafter implements \Magento\Framework\Event\ObserverInterface
{
    protected $_curl;
    protected $_messageManager;
    protected $catalogSession;
    protected $scopeConfig;

    protected $cacheTypeList;
    protected $cacheFrontendPool;

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        TypeListInterface $cacheTypeList, 
        Pool $cacheFrontendPool
    )
    {
        $this->_curl = $curl;
        $this->_messageManager = $messageManager;
        $this->catalogSession = $catalogSession;
        $this->scopeConfig = $scopeConfig;
        $this->cacheManager = $cacheManager;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function flushCache()
    {
    $_types = [
                'full_page'
                ];
    
        foreach ($_types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    private function cleanCache()
    {
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        // or this
        //$this->cacheManager->clean($this->cacheManager->getAvailableTypes());
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$myEventData = $observer->getData('myEventData');
        $_product = $observer->getProduct();  // you will get product object
        $_sku=$_product->getSku(); // for sku
        $_name=$_product->getName();
        $_price=$_product->getPrice();
        $_unidade=$_product->getUnidade();
        $_brand=$_product->getManufacturer();
        $cats = $_product->getCategoryIds();
        $value = $_product->getPontosProduto();
        $barcode = $_product->getBarcode();
        $_active = true;
        $_generatePoints = true;

        // Caso pontos_produto e pontuacao sejam vazio
        // coloca como zero
        if ($value==''){
            $_product->setCustomAttribute('pontos_produto', 0);
            $_product->save();
        }
        if($_product->getPontuacao()==''){
            $_product->setCustomAttribute('pontuacao', 0);
            $_product->save();
        }


        // Pega o token da sessão
        $token = $this->catalogSession->getToken();
        
        // Aqui vai o cnpj do parceiro ecommerce
        // Usa a API GetCurrentPartner para encontrar o cnpj
        $cnpj_partners = [];

        // $token vai ser null caso esteja criando ou atualizando
        // usando a API
        if ($token)
        {
            try{
                $url_base = $this->scopeConfig->getValue('acessos/general/kernel_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $url = $url_base . '/api/Partner/GetCurrentPartner';
                $this->_curl->addHeader("Authorization", 'bearer '.$token);
                $this->_curl->get($url);
                $response = $this->_curl->getBody();
                $dados = json_decode($response);
                $cnpj = $dados->cnpj;
                array_push($cnpj_partners, $cnpj);
            }
            catch (\Exception $e) {
                $this->_messageManager->addError('Não foi possível conectar com germini');
            }
            try{
                $url = $url_base . '/api/Product/CreateUpdateProduct';
                $params = [
                    "sku" => $_sku,
                    "barcode" => [
                        $barcode
                    ],
                    "name" => $_name,
                    "price" => $_price,
                    "unit" => $_unidade,
                    "partners" => $cnpj_partners,
                    "active" => true
                    //"generatePoints" => false
                    // "typeOfPoints" => 0,
                    // "value" => $value
                ];
                
                $data_json = json_encode($params);
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: text/plain'));
    
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
                $response  = curl_exec($ch);
    
                curl_close($ch);
            }
            catch (\Exception $e) {
                $this->_messageManager->addError('Não foi possível conectar com germini');
            }
        } else {
            //$this->cleanCache();
            //$this->flushCache();
        }
    }
}