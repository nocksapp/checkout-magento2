<?php

namespace Magento\NocksPaymentGateway\Model\Methods;


use Magento\Framework\DataObject;

class Ideal extends Nocks
{
	protected $_code = 'nocks_ideal';
	protected $sourceCurrency = 'EUR';

	/**
	 * @param DataObject $data
	 *
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function assignData(DataObject $data)
	{
		parent::assignData($data);

		$additionalData = $data->getAdditionalData();
		if (isset($additionalData['issuer'])) {
			$issuer = $additionalData['issuer'];
			$this->getInfoInstance()->setAdditionalInformation('issuer', $issuer);
		}

		return $this;
	}
}
