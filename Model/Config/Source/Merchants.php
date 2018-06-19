<?php


namespace Magento\NocksPaymentGateway\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\NocksPaymentGateway\Util;
use Nocks\SDK\Constant\Platform;
use Nocks\SDK\NocksApi;

class Merchants implements ArrayInterface {

	private $accessToken;
	private $testMode;

	public function __construct(
		ScopeConfigInterface $scopeConfig
	) {
		$this->accessToken = $scopeConfig->getValue('payment/nocks_gateway/access_token');
		$this->testMode = $scopeConfig->getValue('payment/nocks_gateway/testmode');
	}

	/**
	 * Return array of options as value-label pairs
	 *
	 * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
	 */
	public function toOptionArray() {
		if ($this->accessToken) {
			try {
				$nocksApi = new NocksApi($this->testMode ? Platform::SANDBOX : Platform::PRODUCTION, $this->accessToken);
				$merchants = $nocksApi->merchant->find()->getData();

				return Util::getMerchantsOptions($merchants);
			} catch (\Nocks\SDK\Exception\Exception $e) {
				return [];
			}
		}

		return [];
	}
}