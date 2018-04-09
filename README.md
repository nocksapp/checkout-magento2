# Magento 2: Nocks

**Nocks payment gateway for Magento 2**

## Installation

The Nocks Magento 2 plugin is installed via [Composer](http://getcomposer.org/). To install, simply run:

```
composer require nocksapp/magento2
```

Next run the following commands to activate the module:

```
php bin/magento module:enable Magento_NocksPaymentGateway
php bin/magento setup:upgrade
php bin/magento cache:clean
```

If Magento is running in production mode, deploy the static content:


```
php bin/magento setup:static-content:deploy
```

After the installation. Go to your Magento admin portal, to ‘Stores’ > ‘Configuration’ > ‘Sales’ > ‘Payment Methods’ > ‘Nocks Gateway’.
