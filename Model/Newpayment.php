<?php

namespace Vexpro\CompraPontos\Model;

/**
 * Pay In Store payment method model
 */
class Newpayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
    * Payment code
    *
    * @var string
    */
    protected $_code = 'newpayment';
    const CODE = 'newpayment';

    /**
    * Availability option
    *
    * @var bool
    */
    protected $_isOffline = true;

    /**
     * 
     */
    public function getTeste()
    {
        return 'teste';
    }
}