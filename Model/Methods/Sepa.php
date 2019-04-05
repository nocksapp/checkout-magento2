<?php

namespace Magento\NocksPaymentGateway\Model\Methods;


class Sepa extends Nocks
{
	protected $_code = 'nocks_sepa';
	protected $sourceCurrency = 'EUR';
}
