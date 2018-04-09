<?php


namespace Magento\NocksPaymentGateway\Controller\Callback;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\NocksPaymentGateway\Gateway;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Controller\Result\JsonFactory;


class Index extends Action {

	/** @var Gateway */
	private $gateway;

	/** @var OrderRepositoryInterface */
	private $orderRepository;

	/** @var JsonFactory */
	private $jsonFactory;

	/**
	 * @param Context $context
	 * @param Gateway $gateway
	 * @param OrderRepositoryInterface $orderRepository
	 * @param JsonFactory $jsonFactory
	 */
	public function __construct(
		Context $context,
		Gateway $gateway,
		OrderRepositoryInterface $orderRepository,
		JsonFactory $jsonFactory
	) {
		parent::__construct($context);
		$this->gateway = $gateway;
		$this->orderRepository = $orderRepository;
		$this->jsonFactory = $jsonFactory;
	}

	/**
	 * Handle Nocks callback
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		$transactionId = $entityBody = file_get_contents('php://input');
		$result = $this->jsonFactory->create();

		if ($transactionId) {
			// Get the transaction
			$response = $this->gateway->completePurchase($transactionId);
			$metadata = $response->getMetadata();

			if (isset($metadata['order_id']) && !empty($metadata['order_id'])) {
				// Get order by response meta data
				$order = $this->orderRepository->get($metadata['order_id']);

				if ($order) {
					// Change order state
					if ($response->isSuccessful()) {
						$order->setState(Order::STATE_COMPLETE)
							->setStatus(Order::STATE_COMPLETE)
							->save();
					} else if ($response->isCancelled()) {
						$order->setState(Order::STATE_CANCELED)
							->setStatus(Order::STATE_CANCELED)
							->save();
					}

					return $result->setData(['success' => true]);
				}
			}
		}

		return $result->setData(['success' => false]);
	}
}