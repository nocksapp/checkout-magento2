<?php


namespace Magento\NocksPaymentGateway;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Omnipay\Omnipay;

class Gateway {

	/** @var Omnipay */
	private $gateway;

	/** @var UrlInterface */
	private $urlBuilder;

	public function __construct(
		ScopeConfigInterface $scopeConfig,
		UrlInterface $urlBuilder
	) {
		$accessToken = $scopeConfig->getValue('payment/nocks_gateway/access_token');
		$merchant = $scopeConfig->getValue('payment/nocks_gateway/merchant');

		$gateway = Omnipay::create('Nocks');
		$gateway->setMerchant($merchant);
		$gateway->setAccessToken($accessToken);

		$this->gateway = $gateway;
		$this->urlBuilder = $urlBuilder;
	}

	public function purchase(Order $order) {
		$options = [
			'amount' => $order->getGrandTotal(),
			'currency' => 'EUR',
			'sourceCurrency' => 'NLG',
			'returnUrl' => $this->urlBuilder->getRouteUrl('nocks/redirect', ['order_id' => $order->getId()]),
			'callbackUrl' => $this->urlBuilder->getRouteUrl('nocks/callback'),
			'metadata' => ['order_id' => $order->getId()],
		];

		$response = $this->gateway->purchase($options)->send();

		// Save the Nocks transaction id in the order
		$order->setNocksTransactionId($response->getTransactionReference())->save();

		return $response;
	}

	public function completePurchase($transactionId) {
		return $this->gateway->completePurchase(['transactionReference' => $transactionId])->send();
	}
}
