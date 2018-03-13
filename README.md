# Magento Decred Payments

Magento module, integrates [Decred](https://decred.org) autonomous currency into magento payments.

## Installation

To install package extend your `composer.json` with new module:
```
composer require decred/decred-magento-plugin
```

Enable new extension:
```
php bin/magento module:enable Decred_Payments --clear-static-content
```

Then run next commands to setup new module:
```
php bin/magento setup:upgrade 
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

Read more about how to install magento extensions [here](http://devdocs.magento.com/guides/v2.2/comp-mgr/install-extensions.html)

## Configurations

Decder payments settings located at `Stores -> Configurations -> Sales -> Payment Methods -> Decred Payments`

### Enabled

Enables decred payment method. New payment method available on checkout.

**Ensure to set "Master public key" before setting this config to "Yes".**

### Master public key

In order to receive payments extension need to know your extended public key, from this key new payment addresses
will be generated. Master public key derived from HD chain by path `m/44'/42'/0'`.
Payment address will be derived by for branch 0 with and index is order id: `m/44'/42'/0'/0/{incremental_order_id}` see [BIP44](https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki).

Example: `dpubZH8DiRuE9MyB5rBGmoz3UuQSmTHWKGCQWDs9Jkx73FZuQr1QLTdU9uuwPRbEgEnMYriY9SUr4XshamuoXZC121HVqPXBSFvE57gG9pZd2Ts`


To get it from command line:
```
dcrctl --wallet getmasterpubkey [default]
```

If you activate plugin for old store you should make sure your wallet software can find payment addresses.

In `dcrwallet` command you can increase addresses index gap with `--addridxscanlen` option.

So for old store this address index can be big as first Decred payment `incremental_order_id`.

```
dcrwallet --addridxscanlen {first_incremental_order_id}
```

### Title

Title displayed on checkout payment method select. Default: Decred

### Confirmations to wait

How many confirmations should be on transaction to make it success. Default: 3

### Show refund address?

You can hide refund address input on checkout by setting "No". Default: "Yes"

### Make refund address optional?

By changing setting to "No" you require your customers to fill in refund address. Default: "Yes"
