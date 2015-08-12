# magento2-from-vendor
Load Magento 2 application from the vendor directory


## Steps performed as a part of Experiment

1. Add composer.json from https://github.com/magento/magento2-community-edition/blob/master/composer.json
2. Run composer install --no-plugins
3. Copy project structure from magento2-base
4. Add change-composer.php and run it
5. Regenerate autoload for psr-4 and files with composer dump-autoload
6. Add methods to register Language pack and Theme to Registrar
7. Change \Magento\Framework\App\Filesystem\DirectoryList::MODULES to point to root
8. Fix Setup not to depend on modules paths on app/code. Now application can be installed.
8. Update theme collection to load themes from vendor Magento\Theme\Model\Theme\Collection
9. Update File collectors to load static files from the vendor
10. Disable validation of XML files because XSD is not in place
11. Load template files from the vendor directory
12. Update themes customizations to load from the Themes Directory
13. Update Design/Fallback/RulePool, use module_path and theme_path placeholders instead of full path
14. Introduce ThemeDir class which can provide theme path by theme name and theme name by theme path
16. Modify Controller Action reader to read from modules directories.
15. Point the unit tests to the vendor location

At this point application runs normally. In order to try it, just clone this repository and try to install.


