<?php


namespace Magento\NocksPaymentGateway;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Omnipay\Omnipay;

class Gateway {

	/** @var \Omnipay\Nocks\Gateway */
	private $gateway;

	/** @var String */
	private $merchant;

	/** @var UrlInterface */
	private $urlBuilder;

	public function __construct(
		ScopeConfigInterface $scopeConfig,
		UrlInterface $urlBuilder
	) {
		$accessToken = $scopeConfig->getValue('payment/nocks_gateway/access_token');
		$testmode = $scopeConfig->getValue('payment/nocks_gateway/testmode');
		$this->merchant = $scopeConfig->getValue('payment/nocks_gateway/merchant');

		$gateway = Omnipay::create('Nocks');
		$gateway->setAccessToken($accessToken);
		$gateway->setTestMode($testmode === '1' ? true : false);

		$this->gateway = $gateway;
		$this->urlBuilder = $urlBuilder;
	}

	public function purchase(Order $order) {
		$options = [
			'merchant' => $this->merchant,
			'amount' => $order->getGrandTotal(),
			'currency' => $order->getOrderCurrencyCode(),
			'sourceCurrency' => 'NLG',
			'returnUrl' => $this->urlBuilder->getRouteUrl('nocks/redirect', ['order_id' => $order->getId()]),
			'notifyUrl' => $this->urlBuilder->getRouteUrl('nocks/callback'),
			'metadata' => ['order_id' => $order->getId()],
		];

		$response = $this->gateway->purchase($options)->send();

		// Save the Nocks transaction id in the order
		$order->setNocksTransactionId($response->getTransactionId())->save();

		return $response;
	}

	public function completePurchase($transactionId) {
		return $this->gateway->completePurchase(['transactionId' => $transactionId])->send();
	}
}
