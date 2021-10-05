<?php

namespace Vexpro\CompraPontos\Observer;

use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\View\Element\Template;

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
    ) {
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
        $_types = ["full_page"];

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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $_product = $observer->getProduct(); // you will get product object
        $_sku = $_product->getSku(); // for sku
        $_name = $_product->getName();
        $_price = $_product->getPrice();
        $_unidade = $_product->getUnidade();
        $_brand = $_product->getManufacturer();
        $cats = $_product->getCategoryIds();
        $value = $_product->getPontosProduto();
        $barcode = $_product->getBarcode();
        $_active = true;
        $_generatePoints = true;
        $_origem = $_product->getOrigem();

        // Caso pontos_produto e pontuacao sejam vazios
        // eles terão o valor zero.
        if ($value == "") {
            $_product->setCustomAttribute("pontos_produto", 0);
            //$_product->save();
        }
        if ($_product->getPontuacao() == "") {
            $_product->setCustomAttribute("pontuacao", 0);
            //$_product->save();
        }

        // INTEGRAÇÃO DE IMAGENS DOS PRODUTOS

        // VERIFICA SE O PRODUTO TEM ORIGEM SAP
        if ($_origem == 1) {
            $url_images = $this->scopeConfig->getValue(
                "acessos/general/images_url",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $images_login = $this->scopeConfig->getValue(
                "acessos/general/images_login",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $images_pwd = $this->scopeConfig->getValue(
                "acessos/general/images_pwd",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $productimages = [];
            $imageUrls = [];
            $productimages = $_product->getMediaGalleryImages();
            foreach ($productimages as $productimage) {
                array_push($imageUrls, $productimage["url"]);
            }

            $logger = $objectManager->create("\Psr\Log\LoggerInterface");

            $Url_Imagem = isset($imageUrls[0]) ? $imageUrls[0] : "";
            $Url_Imagem2 = isset($imageUrls[1]) ? $imageUrls[1] : "";
            $Url_Imagem3 = isset($imageUrls[2]) ? $imageUrls[2] : "";

            $xmlstr = "<?xml version='1.0' encoding='UTF-8'?>
                        <soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:urn=\"urn:sap-com:document:sap:rfc:functions\">
                        <soapenv:Header/>
                        <soapenv:Body>
                            <urn:Z_Replicacao_Imagem_Produto>
                                <sku>{$_sku}</sku>
                                <Url_Imagem>{$Url_Imagem}</Url_Imagem>
                                <Url_Imagem2>{$Url_Imagem2}</Url_Imagem2>
                                <Url_Imagem3>{$Url_Imagem3}</Url_Imagem3>
                            </urn:Z_Replicacao_Imagem_Produto>
                        </soapenv:Body>
                    </soapenv:Envelope>
                ";

            $simplexml = new \SimpleXMLElement($xmlstr);

            $input_xml = $simplexml->asXML();

            $logger->info("Enviado ao SAP: " . $input_xml);

            //setting the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_images);
            // Following line is compulsary to add as it is:
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input_xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml"]);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_USERPWD, $images_login . ":" . $images_pwd);

            // $data = curl_exec($ch);
            // curl_close($ch);

            // 		if ($data == "")
            // 		{
            // 			throw new \Exception('Erro na comunicação com repositório de imagens.');
            // 		}

            // $logger->info("Resposta SAP: " . $data);
        }



        // Pega o token da sessão
        $token = $this->catalogSession->getToken();

        // Aqui vai o cnpj do parceiro ecommerce
        // Usa a API GetCurrentPartner para encontrar o cnpj
        $cnpj_partners = [];

        // $token vai ser null caso esteja criando ou atualizando
        // usando a API
        if ($token) {
            try {
                $url_base = $this->scopeConfig->getValue(
                    "acessos/general/kernel_url",
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $url = $url_base . "/api/Partner/GetCurrentPartner";
                $this->_curl->addHeader("Authorization", "bearer " . $token);
                $this->_curl->get($url);
                $response = $this->_curl->getBody();
                $dados = json_decode($response);

                $branches = $dados->branches;
                foreach ($branches as $branch) {
                    array_push($cnpj_partners, $branch->cnpj);
                }
                // $cnpj = $dados->cnpj;
                // array_push($cnpj_partners, $cnpj);
            } catch (\Exception $e) {
                $this->_messageManager->addError(
                    "Não foi possível conectar com germini"
                );
            }
            try {
                $url = $url_base . "/api/Product/CreateUpdateProduct";
                $params = [
                    "sku" => $_sku,
                    "barcode" => [$barcode],
                    "name" => $_name,
                    "price" => $_price,
                    "unit" => $_unidade,
                    "partners" => $cnpj_partners,
                    "active" => true,
                ];

                $data_json = json_encode($params);

                // $logger->info("Enviado ao Germini: " . $data_json);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Content-Type: application/json",
                    "Accept: text/plain",
                    "x-api-key: lpszGTo6WAwoKbFzrtd1sIIwvknz40sD",
                ]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                $data = json_decode($response);
                if ($data->success == false) {
                    foreach ($data->errors as $erro) {
                        $this->_messageManager->addError($erro->message);
                    }
                }

                curl_close($ch);
            } catch (\Exception $e) {
                $this->_messageManager->addError(
                    "Não foi possível conectar com germini"
                );
            }
        }
    }
}
