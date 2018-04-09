<?php


namespace Magento\NocksPaymentGateway\Controller\StartPayment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\NocksPaymentGateway\Gateway;


class Index extends Action {

	/** @var Session */
	private $checkoutSession;

	/** @var Gateway */
	private $gateway;

	/**
	* @param Context $context
	* @param Session $checkoutSession
	* @param Gateway $gateway
	*/
	public function __construct(
		Context $context,
		Session $checkoutSession,
		Gateway $gateway
	) {
		parent::__construct($context);
		$this->checkoutSession = $checkoutSession;
		$this->gateway = $gateway;
	}

	/**
	* Initialize redirect to Nocks
	*/
	public function execute() {
		$order = $this->checkoutSession->getLastRealOrder();

		if (!$order) {
			return $this->_redirect('checkout/cart');
		}

		$response = $this->gateway->purchase($this->checkoutSession->getLastRealOrder());
		return $response->redirect();
	}
}