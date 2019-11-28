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
		$this->merchant = $scopeConfig->getValue('payment/nocks_gateway/merchant');
		$this->gateway = Util::makeOmnipayGateway($scopeConfig);
		$this->urlBuilder = $urlBuilder;
	}

	public function purchase(Order $order, $data) {
		$options = array_merge([
			'merchant' => $this->merchant,
			'amount' => $order->getGrandTotal(),
			'currency' => $order->getOrderCurrencyCode(),
			'returnUrl' => $this->urlBuilder->getRouteUrl('nocks/redirect', ['order_id' => $order->getId()]),
			'notifyUrl' => $this->urlBuilder->getRouteUrl('nocks/callback'),
			'metadata' => [
				'order_id' => $order->getId(),
				'nocks_plugin' => 'magento2:1.4.0',
			],
			'description' => $order->getRealOrderId() . ' - ' . $order->getStore()->getFrontendName(),
		], $data);

		$response = $this->gateway->purchase($options)->send();

		// Save the Nocks transaction id in the order
		$order->setNocksTransactionId($response->getTransactionId())->save();

		return $response;
	}

	public function completePurchase($transactionId) {
		return $this->gateway->completePurchase(['transactionId' => $transactionId])->send();
	}
}
