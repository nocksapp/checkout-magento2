<?php


namespace Magento\NocksPaymentGateway\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

use Magento\NocksPaymentGateway\Util;
use Nocks\SDK\Constant\Platform;
use Nocks\SDK\Exception\Exception;
use Nocks\SDK\NocksApi;

class Merchants extends Action {
	
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
			$nocksApi = new NocksApi($params['testMode'] === '0' ? Platform::PRODUCTION : Platform::SANDBOX, $params['accessToken']);
			$merchants = $nocksApi->merchant->find()->getData();
			
			return $result->setData(['merchants' => Util::getMerchantsOptions($merchants)]);
		} catch (Exception $exception) {
			return $result->setData(['merchants' => []]);
		}

		return $result->setData(['merchants' => []]);
	}
}