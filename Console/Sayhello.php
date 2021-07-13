<?php
namespace Vexpro\CompraPontos\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Sayhello extends Command
{
    const USERNAME = "username";
    const PASSWORD = "password";

    protected $productCollectionFactory;
    protected $productStatus;
    protected $productVisibility;
    protected $catalogSession;
    protected $scopeConfig;
    protected $_curl;


    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        parent::__construct();
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->catalogSession = $catalogSession;
        $this->scopeConfig = $scopeConfig;
        $this->_curl = $curl;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts()
    {
        $collection = $this->productCollectionFactory->create();
        // $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        // $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        // $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
        // 		->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

        return $collection->getItems();
    }
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::USERNAME,
                null,
                InputOption::VALUE_REQUIRED,
                "username"
            ),
            new InputOption(
                self::PASSWORD,
                null,
                InputOption::VALUE_REQUIRED,
                "password"
            ),
        ];

        $this->setName("vexpro:saveproducts")
            ->setDescription("Save all products in magento integrating with Germini")
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption(self::USERNAME);
        $password = $input->getOption(self::PASSWORD);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $url_base = $this->scopeConfig->getValue('acessos/general/identity_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $state = $objectManager->get("Magento\Framework\App\State");
        $state->setAreaCode("frontend");

        $response = "";
        $url = $url_base . "/connect/token";
        $params = [
            "username" => $username,
            "password" => $password,
            "client_id" => "ro.client.partner",
            "client_secret" => "secret",
            "grant_type" => "password",
            "scope" => "germini-api openid profile",
        ];
        $this->_curl->post($url, $params);
        //response will contain the output in form of JSON string
        $response = $this->_curl->getBody();
        $resultado = json_decode($response);

        if ($response == "" || isset($resultado->error)) {
            $output->writeln("<error>Error connecting to Germini.</error>");
            return;
        }

        $token = json_decode($response)->access_token;

        $this->catalogSession->setData("token", $token);

        $products = $this->getProducts();
        foreach ($products as $prod) {
            $product = $objectManager
                ->create("Magento\Catalog\Model\Product")
                ->load($prod->getId());
            $product->save();
            $output->writeln("<info>Saved product with id</info>" . $prod->getId());
        }
        $output->writeln("<info>All products saved successfully!</info>");

    }
}
