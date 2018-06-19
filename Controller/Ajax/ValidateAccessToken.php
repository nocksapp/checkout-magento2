<?php


namespace Magento\NocksPaymentGateway\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

use Nocks\SDK\Constant\Platform;
use Nocks\SDK\Exception\Exception;
use Nocks\SDK\NocksOauth;

class ValidateAccessToken extends Action {

	private $requiredScopes = ['merchant.read', 'transaction.create', 'transaction.read'];

	/** @var JsonFactory */
	private $jsonFactory;

	/**
	 * @param Context $context
	 * @param JsonFactory $jsonFactory
	 */
	public function __construct(
		Context $context,
		JsonFactory $jsonFactory
	) {
		parent::__construct($context);
		$this->jsonFactory = $jsonFactory;
	}

	/**
	 * Handle validation
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$result = $this->jsonFactory->create();

		$params = $this->getRequest()->getParams();

		try {
			$nocksOauth = new NocksOauth($params['testMode'] === '0' ? Platform::PRODUCTION : Platform::SANDBOX);
			$scopes = $nocksOauth->tokenScopes($params['accessToken']);

			$requiredAccessTokenScopes = array_filter(array_map(function($scope) {
				return $scope['id'];
			}, $scopes), function($scope) {
				return in_array($scope, $this->requiredScopes);
			});

			return $result->setData(['valid' => sizeof($requiredAccessTokenScopes) === sizeof($this->requiredScopes)]);
		} catch (Exception $exception) {
			return $result->setData(['valid' => false]);
		}

		return $result->setData(['valid' => false]);
	}
}