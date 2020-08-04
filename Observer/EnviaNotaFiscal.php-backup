<?php
namespace Vexpro\CompraPontos\Observer;

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Common\Standardize;


class EnviaNotaFiscal implements \Magento\Framework\Event\ObserverInterface
{

    protected $driverFile;


    public function __construct(\Magento\Framework\Filesystem\Driver\File $driverFile)
    {
        $this->driverFile = $driverFile;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $existe = $this->driverFile->isExists("./certificado.crt");

        $order = $observer->getEvent()->getOrder();
        $estado = $order->getState();

        // TODO: Pegar os dados da compra para preencher os dados

        if ($estado == 'processing'){
            // Enviar os dados para receita federal (NOTA FISCAL)
            // Ambientes:
            // 1 - Produção (imposto)
            // 2- Homologação (teste)

            $nfe = new Make();
            $std = new \stdClass();
            
            $std->versao = '4.00';
            $std->Id = null;
            $std->pk_nItem = '';
            $nfe->taginfNFe($std);
            

            $std = new \stdClass();
            $std->cUF = 35; //coloque um código real e válido
            $std->cNF = '80070008';
            $std->natOp = 'VENDA';
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = 10;
            $std->dhEmi = '2018-07-27T20:48:00-02:00';
            $std->dhSaiEnt = '2018-07-27T20:48:00-02:00';
            $std->tpNF = 1;
            $std->idDest = 1;
            $std->cMunFG = 3506003; //Código de município precisa ser válido
            $std->tpImp = 1;
            $std->tpEmis = 1;
            $std->cDV = 2;
            $std->tpAmb = 2; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
            $std->finNFe = 1;
            $std->indFinal = 0;
            $std->indPres = 0;
            $std->procEmi = '0';
            $std->verProc = 1;
            $nfe->tagide($std);
            
            $std = new \stdClass();
            $std->xNome = 'C.VALE - COOPERATIVA AGROINDUSTRIAL';
            $std->IE = '4140122306';
            $std->CRT = 3;
            $std->CNPJ = '77863223000530';
            $nfe->tagemit($std);
            
            $std = new \stdClass();
            $std->xLgr = "Rua Teste";
            $std->nro = '203';
            $std->xBairro = 'Centro';
            $std->cMun = 3506003; //Código de município precisa ser válido e igual o  cMunFG
            $std->xMun = 'Bauru';
            $std->UF = 'SP';
            $std->CEP = '80045190';
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $nfe->tagenderEmit($std);
            
            $std = new \stdClass();
            $std->xNome = 'Empresa destinatário teste';
            $std->indIEDest = 1;
            $std->IE = '4140122306';
            $std->CNPJ = '77863223000530';
            $nfe->tagdest($std);
            
            $std = new \stdClass();
            $std->xLgr = "Rua Teste";
            $std->nro = '203';
            $std->xBairro = 'Centro';
            $std->cMun = '3506003';
            $std->xMun = 'Bauru';
            $std->UF = 'SP';
            $std->CEP = '80045190';
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $nfe->tagenderDest($std);
            
            
            // foreach ($order->getAllItems() as $item) {
            //     $preco = $item->getPrice();
            //     $codigo = $item->getItemId();
            //     $sku = $item->getSku();

            //     $std = new \stdClass();
            //     $std->item = 1;
            //     $std->cProd = $codigo;
            //     $std->xProd = $sku;
            //     $std->NCM = '66554433';
            //     $std->CFOP = '5102';
            //     $std->uCom = 'PÇ';
            //     $std->qCom = '1.0000';
            //     $std->vUnCom = $preco;
            //     $std->vProd = '840.00';
            //     $std->uTrib = 'PÇ';
            //     $std->qTrib = '1.0000';
            //     $std->vUnTrib = $preco;
            //     $std->indTot = 1;
            //     $nfe->tagprod($std);
            // }

            $std = new \stdClass();
            $std->item = 1;
            $std->cEAN = 'SEM GTIN';
            $std->cEANTrib = 'SEM GTIN';
            $std->cProd = '0001';
            $std->xProd = 'Produto teste';
            $std->NCM = '84669330';
            $std->CFOP = '5102';
            $std->uCom = 'PÇ';
            $std->qCom = '1.0000';
            $std->vUnCom = '10.99';
            $std->vProd = '10.99';
            $std->uTrib = 'PÇ';
            $std->qTrib = '1.0000';
            $std->vUnTrib = '10.99';
            $std->indTot = 1;
            $nfe->tagprod($std);            
            
            $std = new \stdClass();
            $std->item = 1;
            $std->vTotTrib = 10.99;
            $nfe->tagimposto($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->orig = 0;
            $std->CST = '00';
            $std->modBC = 0;
            $std->vBC = '0.20';
            $std->pICMS = '18.0000';
            $std->vICMS = '0.04';
            $nfe->tagICMS($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->cEnq = '999';
            $std->CST = '50';
            $std->vIPI = 0;
            $std->vBC = 0;
            $std->pIPI = 0;
            $nfe->tagIPI($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->CST = '07';
            $std->vBC = 0;
            $std->pPIS = 0;
            $std->vPIS = 0;
            $nfe->tagPIS($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->vCOFINS = 0;
            $std->vBC = 0;
            $std->pCOFINS = 0;
            $nfe->tagCOFINSST($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->CST = '01';
            $std->vBC = 0;
            $std->pCOFINS = 0;
            $std->vCOFINS = 0;
            $std->qBCProd = 0;
            $std->vAliqProd = 0;
            $nfe->tagCOFINS($std);
            
            $std = new \stdClass();
            $std->vBC = '0.20';
            $std->vICMS = 0.04;
            $std->vICMSDeson = 0.00;
            $std->vBCST = 0.00;
            $std->vST = 0.00;
            $std->vProd = 10.99;
            $std->vFrete = 0.00;
            $std->vSeg = 0.00;
            $std->vDesc = 0.00;
            $std->vII = 0.00;
            $std->vIPI = 0.00;
            $std->vPIS = 0.00;
            $std->vCOFINS = 0.00;
            $std->vOutro = 0.00;
            $std->vNF = 11.03;
            $std->vTotTrib = 0.00;
            $nfe->tagICMSTot($std);
            
            $std = new \stdClass();
            $std->modFrete = 1;
            $nfe->tagtransp($std);
            
            $std = new \stdClass();
            $std->item = 1;
            $std->qVol = 2;
            $std->esp = 'caixa';
            $std->marca = 'OLX';
            $std->nVol = '11111';
            $std->pesoL = 10.00;
            $std->pesoB = 11.00;
            $nfe->tagvol($std);
            
            $std = new \stdClass();
            $std->nFat = '100';
            $std->vOrig = 100;
            $std->vLiq = 100;
            $nfe->tagfat($std);
            
            $std = new \stdClass();
            $std->nDup = '001';
            $std->dVenc = date('Y-m-d');
            $std->vDup = 11.03;
            $nfe->tagdup($std);
            
            $std = new \stdClass();
            $std->vTroco = 0;
            $nfe->tagpag($std);

            $std = new \stdClass();
            $std->indPag = 0;
            $std->tPag = "01";
            $std->vPag = 10.99;
            $std->indPag=0;
            $nfe->tagdetPag($std);

            $xml = $nfe->getXML(); // O conteúdo do XML fica armazenado na variável $xml
  

            // $teste = var_dump($xml);
            // $foi = file_put_contents('teste.xml', $xml);

            $config  = [
                "atualizacao"=>date('Y-m-d h:i:s'),
                "tpAmb"=> 2,
                "razaosocial" => "C.VALE - COOPERATIVA AGROINDUSTRIAL",
                "cnpj" => "77863223000530", // PRECISA SER VÁLIDO
                "ie" => '4140122306', // PRECISA SER VÁLIDO
                "siglaUF" => "PR",
                "schemes" => "PL_009_V4",
                "versao" => '4.00',
                "tokenIBPT" => "AAAAAAA",
                "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
                "CSCid" => "000002",
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                    ]
                ];
             $configJson = json_encode($config);

             $certificadoDigital = file_get_contents("certificado.pfx");



             try {

                $certificate = Certificate::readPfx($certificadoDigital, '123456');
                $tools = new Tools($configJson, $certificate);
                $tools->model('55');
            
                $uf = 'PR';
                $cnpj = '77863223000530';
                $iest = '';
                $cpf = '';
                $response = $tools->sefazCadastro($uf, $cnpj, $iest, $cpf);
            
                //você pode padronizar os dados de retorno atraves da classe abaixo
                //de forma a facilitar a extração dos dados do XML
                //NOTA: mas lembre-se que esse XML muitas vezes será necessário, 
                //      quando houver a necessidade de protocolos
                $stdCl = new Standardize($response);
                //nesse caso $std irá conter uma representação em stdClass do XML
                $std = $stdCl->toStd();
                //nesse caso o $arr irá conter uma representação em array do XML
                $arr = $stdCl->toArray();
                //nesse caso o $json irá conter uma representação em JSON do XML
                $json = $stdCl->toJson();
            
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            

            //  $certificadoDigital = file_get_contents('certificado.pfx');
            $tools = new Tools($configJson, Certificate::readPfx($certificadoDigital, '123456'));

            try {
                $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
            } catch (\Exception $e) {
                //aqui você trata possíveis exceptions da assinatura
                exit($e->getMessage());
            }

            try {
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                // $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote);
                $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote);

                // $xmls = array();
                // $resp = $tools->sefazEnviaLote(array($xmlAssinado), $idLote, 1, false, $xmls);
            
                $st = new NFePHP\NFe\Common\Standardize();
                $std = $st->toStd($resp);
                if ($std->cStat != 103) {
                    //erro registrar e voltar
                    exit("[$std->cStat] $std->xMotivo");
                }
                $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota
            } catch (\Exception $e) {
                //aqui você trata possiveis exceptions do envio
                exit($e->getMessage());
            }
        }
    }
}