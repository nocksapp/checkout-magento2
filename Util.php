<?php

namespace Magento\NocksPaymentGateway;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Omnipay\Omnipay;

class Util {

	/**
	 * @param $merchants
	 *
	 * @return array
	 */
	public static function getMerchantsOptions($merchants) {
		$options = [];
		foreach ($merchants as $merchant) {
			$merchantName = $merchant->getName();
			foreach ($merchant->getMerchantProfiles() as $profile) {
				$profileName = $profile->getName();
				$label = ($merchantName === $profileName ? $merchantName : $merchantName . ' (' . $profileName . ')')
				         . ' (' . $merchant->getCoc() . ')';

				$options[] = [
					'value' => $profile->getUuid(),
					'label' => htmlentities($label, ENT_COMPAT, 'UTF-8'),
				];
			}
		}

		return $options;
	}

	/**
	 * @param ScopeConfigInterface $scopeConfig
	 *
	 * @return \Omnipay\Common\GatewayInterface
	 */
	public static function makeOmnipayGateway(ScopeConfigInterface $scopeConfig) {
		$accessToken = $scopeConfig->getValue('payment/nocks_gateway/access_token');
		$testmode = $scopeConfig->getValue('payment/nocks_gateway/testmode');

		$gateway = Omnipay::create('Nocks');
		$gateway->setAccessToken($accessToken);
		$gateway->setTestMode($testmode === '1' ? true : false);

		return $gateway;
	}
}