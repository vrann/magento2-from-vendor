# magento2-from-vendor
Load Magento 2 application from the vendor directory


## Steps to Reproduce Experiment

1. Add composer.json from https://github.com/magento/magento2-community-edition/blob/master/composer.json
2. Run composer install --no-plugins
3. Copy project structure from magento2-base
4. Add change-composer.php and run it
5. Regenerate autoload for psr-4 and files with composer dump-autoload
