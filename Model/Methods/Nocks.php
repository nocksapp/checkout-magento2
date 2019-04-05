<?php

namespace Magento\NocksPaymentGateway\Model\Methods;

use Magento\Framework\DataObject;
use Magento\Payment\Model\Method\AbstractMethod;


class Nocks extends AbstractMethod
{
	protected $sourceCurrency = null;

	/**
	 * @param string $currencyCode
	 *
	 * @return bool
	 */
	public function canUseForCurrency($currencyCode)
	{
		return $currencyCode === 'EUR';
	}

	/**
	 * @param DataObject $data
	 *
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function assignData(DataObject $data)
	{
		parent::assignData($data);

		if ($this->sourceCurrency) {
			$this->getInfoInstance()->setAdditionalInformation('sourceCurrency', $this->sourceCurrency);
		}

		return $this;
	}
}
