# magento2-from-vendor

Load Magento 2 application from the vendor directory

# Steps to Reproduce Experiment

## Initial checkout

1. Get composer.json from https://github.com/magento/magento2-community-edition/blob/master/composer.json
2. composer install --no-plugins

## Get Ready for Installation

1. updated installed.json
2. add modules registration to each module
3. run composer dump-autoload
4. Change Setup logic which depends on class path (MODULES constant usage and array keys with the path)
5. Setup application
