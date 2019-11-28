<?php


namespace Magento\NocksPaymentGateway\Model\Ui;

use Magento\Framework\App\CacheInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\NocksPaymentGateway\Util;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
	private $cache;
	private $gateway;
	
	public function __construct(
		ScopeConfigInterface $scopeConfig,
		CacheInterface $cache
	) {
		$this->cache = $cache;
		$this->gateway = Util::makeOmnipayGateway($scopeConfig);
	}

	private function formatIssuers($issuers) {
		$arr = [];
		foreach ($issuers as $id => $label) {
			$arr[] = ['id' => $id, 'label' => $label];
		}

		return $arr;
	}

	private function getIssuers() {
		$cacheKey = 'nocks_issuers_' . ($this->gateway->getTestMode() ? 'test' : 'live');
		$cachedIssuers = $this->cache->load($cacheKey);

		if ($cachedIssuers) {
			return $this->formatIssuers(json_decode($cachedIssuers, true));
		}

		// Cache the issuers for a day
		$issuers = [];
		foreach ($this->gateway->fetchIssuers()->send()->getIssuers() as $issuer) {
			$issuers[$issuer->getId()] = $issuer->getName();
		}

		$this->cache->save(json_encode($issuers), $cacheKey, [], 60 * 60 * 24);

		return $this->formatIssuers($issuers);
	}

	/**
	 * Retrieve assoc array of checkout configuration
	 *
	 * @return array
	 */
	public function getConfig()
	{
		return [
			'payment' => [
				'issuers' => array_merge([['id' => '', 'label' => __('Select your bank')]], $this->getIssuers()),
			],
		];
	}
}
