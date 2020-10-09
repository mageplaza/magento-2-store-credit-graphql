# Magento 2 Store Credit GraphQL (Support PWA)

Mageplaza Store Credit Extension supports getting and pushing data on the website with GraphQl.

## How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-store-credit-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## How to use

- Mageplaza's Store Credit Extension supports getting customer information and transactions, retrieving product information and using credits through GraphQL.
- Note: To perform GraphQL queries in Magento you need to use Magento 2.3.x and return the site to developer mode.
- Refer to the GraphQl requests we support [here](https://documenter.getpostman.com/view/6685698/SzKVRdWF?version=latest).
