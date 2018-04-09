<?php


namespace Magento\NocksPaymentGateway\Setup;


use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;

class InstallData implements InstallDataInterface {

	/**
	 * Sales setup factory
	 *
	 * @var SalesSetupFactory
	 */
	private $salesSetupFactory;

	/**
	 * Init
	 *
	 * @param SalesSetupFactory $salesSetupFactory
	 */
	public function __construct(
		SalesSetupFactory $salesSetupFactory
	) {
		$this->salesSetupFactory = $salesSetupFactory;
	}

	/**
	 * Installs data for a module
	 *
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 *
	 * @return void
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		/** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
		$salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

		$salesSetup->addAttribute('order', 'nocks_transaction_id', [
			'type' => 'varchar',
			'visible' => false,
			'required' => false
		]);
	}
}