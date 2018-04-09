<?php


namespace Magento\NocksPaymentGateway\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Merchants implements ArrayInterface {

	protected $url = 'https://api.nocks.com/api/v2/merchant';

	private $accessToken;

	public function __construct(
		ScopeConfigInterface $scopeConfig
	) {
		$this->accessToken = $scopeConfig->getValue('payment/nocks_gateway/access_token');
	}

	/**
	 * Return array of options as value-label pairs
	 *
	 * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
	 */
	public function toOptionArray() {
		$curl = curl_init($this->url);
		$header = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $this->accessToken,
		];

		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

		$responseString = curl_exec($curl);

		if (!$responseString) {
			return [];
		}

		$response = json_decode($responseString, true);

		curl_close($curl);

		if (!isset($response['status']) || $response['status'] !== 200) {
			return [];
		}

		$options = [];
		foreach ($response['data'] as $merchant) {
			$merchantName = $merchant['name'];
			foreach ($merchant['merchant_profiles']['data'] as $profile) {
				$label = ($merchantName === $profile['name'] ? $merchantName : $merchantName . ' (' . $profile['name'] . ')')
					. ' (' . $merchant['coc'] . ')';

				$options[] = [
					'value' => $profile['uuid'],
					'label' => htmlentities($label, ENT_COMPAT, 'UTF-8'),
				];
			}
		}

		return $options;
	}
}