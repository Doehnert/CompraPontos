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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

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
            //$_product->save();
        }
        if($_product->getPontuacao()==''){
            $_product->setCustomAttribute('pontuacao', 0);
            //$_product->save();
        }

        // INTEGRAÇÃO DE IMAGENS DOS PRODUTOS
        $url_images = $this->scopeConfig->getValue('acessos/general/images_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        $productimages = array();
        $imageUrls = array();
        $productimages = $_product->getMediaGalleryImages();
        foreach($productimages as $productimage)
        {
         array_push($imageUrls, $productimage['url']);
        }

        $logger = $objectManager->create("\Psr\Log\LoggerInterface");

        $Url_Imagem = isset($imageUrls[0]) ? $imageUrls[0] : '';
        $Url_Imagem2 = isset($imageUrls[1]) ? $imageUrls[1] : '';
        $Url_Imagem3 = isset($imageUrls[2]) ? $imageUrls[2] : '';


        $xmlstr = "<?xml version='1.0' encoding='UTF-8'?>
        <definitions targetNamespace='urn:sap-com:document:sap:rfc:functions' xmlns='http://schemas.xmlsoap.org/wsdl/' xmlns:wsp='http://schemas.xmlsoap.org/ws/2004/09/policy' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd' xmlns:http='http://schemas.xmlsoap.org/wsdl/http/' xmlns:s0='urn:sap-com:document:sap:rfc:functions' xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/'>
            <types>
                <xsd:schema targetNamespace='urn:sap-com:document:sap:rfc:functions'>
                        <xsd:element name='Z_Replicacao_Imagem_Produto'>
                            <xsd:complexType>
                                <xsd:all>
                                    <xsd:element name='{$_sku}' type='xsd:string'/>
                                    <xsd:element minOccurs='0' name='{$Url_Imagem}' type='xsd:string'/>
                                    <xsd:element minOccurs='0' name='{$Url_Imagem2}' type='xsd:string'/>
                                    <xsd:element minOccurs='0' name='{$Url_Imagem3}' type='xsd:string'/>
                                </xsd:all>
                            </xsd:complexType>
                        </xsd:element>

                    <xsd:element name='Z_Replicacao_Imagem_Produto.Response'>
                        <xsd:complexType>
                        <xsd:all>
                            <xsd:element name='E_RESPONSE'>
                                <xsd:complexType>
                                    <xsd:sequence>
                                    <xsd:element minOccurs='0' name='Status' type='xsd:string'/>
                                    <xsd:element minOccurs='0' name='Msg' type='xsd:string'/>
                                    </xsd:sequence>
                                </xsd:complexType>
                            </xsd:element>
                        </xsd:all>
                        </xsd:complexType>
                    </xsd:element>
                    </xsd:schema>
            </types>
            <message name='Z_Replicacao_Imagem_ProdutoOutput'>
                <part name='parameters' element='s0:Z_Replicacao_Imagem_Produto.Response'>
                </part>
            </message>
            <message name='Z_Replicacao_Imagem_ProdutoInput'>
                <part name='parameters' element='s0:Z_Replicacao_Imagem_Produto'>
                </part>
            </message>
            <portType name='Z_Replicacao_Imagem_Produto_PortType'>
                <operation name='Z_Replicacao_Imagem_Produto'>
                <input message='s0:Z_Replicacao_Imagem_ProdutoInput'>
                </input>
                <output message='s0:Z_Replicacao_Imagem_ProdutoOutput'>
                </output>
                </operation>
            </portType>
            <binding name='Z_Replicacao_Imagem_Produto_ServiceBinding' type='s0:Z_Replicacao_Imagem_Produto_PortType'>
                <soap:binding style='document' transport='http://schemas.xmlsoap.org/soap/http'/>
                <wsp:Policy xmlns:wsp='http://schemas.xmlsoap.org/ws/2004/09/policy'>
                        <wsp:PolicyReference URI='#BN__binding'/>
                    </wsp:Policy>
                <operation name='Z_Replicacao_Imagem_Produto'>
                <soap:operation soapAction='http://www.sap.com/Z_Replicacao_Imagem_Produto'/>
                <input>
                    <soap:body use='literal'/>
                </input>
                <output>
                    <soap:body use='literal'/>
                </output>
                </operation>
            </binding>
            <service name='Z_Replicacao_Imagem_Produto_Service'>
            <documentation>SAP Service Z_Replicacao_Produto via SOAP to MKT</documentation>
                <port name='Z_Replicacao_Imagem_ProdutoPortType' binding='s0:Z_Replicacao_Imagem_Produto_ServiceBinding'>
                <soap:address location='https://e400237-iflmap.hcisbt.br1.hana.ondemand.com:443/cxf/Replicacao_Imagens_Produtos'/>
                </port>
            </service>
                <wsp:UsingPolicy required='true' xmlns:wsp='http://schemas.xmlsoap.org/ws/2004/09/policy'/>
                <wsp:Policy wsu:Id='BN__binding' xmlns:wsp='http://schemas.xmlsoap.org/ws/2004/09/policy' xmlns:wsu='http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'>
                    <wsp:ExactlyOne>
                        <wsp:All>
                            <sp:TransportBinding xmlns:sp='http://docs.oasis-open.org/ws-sx/ws-securitypolicy/200702'>
                                <wsp:Policy>
                                    <sp:TransportToken>
                                        <wsp:Policy>
                                            <sp:HttpsToken>
                                                <wsp:Policy>
                                                    <wsp:ExactlyOne>
                                                        <sp:HttpBasicAuthentication/>
                                                        <sp:RequireClientCertificate/>
                                                    </wsp:ExactlyOne>
                                                </wsp:Policy>
                                            </sp:HttpsToken>
                                        </wsp:Policy>
                                    </sp:TransportToken>
                                    <sp:AlgorithmSuite>
                                        <wsp:Policy>
                                            <wsp:ExactlyOne>
                                                <sp:Basic256/>
                                                <sp:Basic192/>
                                                <sp:Basic128/>
                                                <sp:TripleDes/>
                                                <sp:Basic256Rsa15/>
                                                <sp:Basic192Rsa15/>
                                                <sp:Basic128Rsa15/>
                                                <sp:TripleDesRsa15/>
                                                <sp:Basic256Sha256/>
                                                <sp:Basic192Sha256/>
                                                <sp:Basic128Sha256/>
                                                <sp:TripleDesSha256/>
                                                <sp:Basic256Sha256Rsa15/>
                                                <sp:Basic192Sha256Rsa15/>
                                                <sp:Basic128Sha256Rsa15/>
                                                <sp:TripleDesSha256Rsa15/>
                                            </wsp:ExactlyOne>
                                        </wsp:Policy>
                                    </sp:AlgorithmSuite>
                                    <sp:Layout>
                                        <wsp:Policy>
                                            <sp:Strict/>
                                        </wsp:Policy>
                                    </sp:Layout>
                                </wsp:Policy>
                            </sp:TransportBinding>
                        </wsp:All>
                    </wsp:ExactlyOne>
                </wsp:Policy>
            </definitions>
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
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: text/xml",
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);

            $data = curl_exec($ch);
            curl_close($ch);

            $logger->info("Resposta SAP: " . $data);



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
                $this->_curl->addHeader('Authorization', 'bearer '.$token);
                $this->_curl->get($url);
                $response = $this->_curl->getBody();
                $dados = json_decode($response);

                $branches = $dados->branches;
                foreach($branches as $branch)
                {
                    array_push($cnpj_partners, $branch->cnpj);
                }
                // $cnpj = $dados->cnpj;
                // array_push($cnpj_partners, $cnpj);
            }
            catch (\Exception $e) {
                $this->_messageManager->addError('Não foi possível conectar com germini');
            }
            try{
                $url = $url_base . '/api/Product/CreateUpdateProduct';
                $params = [
                    'sku' => $_sku,
                    'barcode' => [
                        $barcode
                    ],
                    'name' => $_name,
                    'price' => $_price,
                    'unit' => $_unidade,
                    'partners' => $cnpj_partners,
                    'active' => true
                ];

                $data_json = json_encode($params);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: text/plain', 'x-api-key: lpszGTo6WAwoKbFzrtd1sIIwvknz40sD'));

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response  = curl_exec($ch);
                $data = json_decode($response);
                if ($data->success == false){
                    foreach($data->errors as $erro)
                    {
                        $this->_messageManager->addError($erro->message);
                    }

                }

                curl_close($ch);
            }
            catch (\Exception $e) {
                $this->_messageManager->addError('Não foi possível conectar com germini');
            }
        }
    }
}
