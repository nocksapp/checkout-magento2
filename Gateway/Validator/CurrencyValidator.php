<?php


namespace Magento\NocksPaymentGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;


class CurrencyValidator extends AbstractValidator
{
	/**
	 * @inheritdoc
	 */
	public function validate(array $validationSubject)
	{
		return $this->createResult(in_array($validationSubject['currency'], ['EUR']));
	}
}