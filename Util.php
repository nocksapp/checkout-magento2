<?php

namespace Magento\NocksPaymentGateway;


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
}