<?php


foreach (new DirectoryIterator('vendor/magento') as $fileInfo) {
    if ($fileInfo->isDot()) {
        continue;
    }

    $file = $fileInfo->getRealPath() . "/composer.json";
    $json = file_get_contents($file);
    $decoded = json_decode($json, true);

    if (isset($decoded['extra']['map'])) {
        $moduleName = str_replace('/', "_", $decoded['extra']['map'][0][1]);
        $classPath = str_replace('/', "\\", $decoded['extra']['map'][0][1]) . "\\";
        var_dump($decoded['extra']['map'][0][1], $decoded['type']);

        $registerFunction = null;
        if ($decoded['type'] == 'magento2-module') {
            $registerFunction = 'registerModule';
        } else if ($decoded['type'] == 'magento2-theme') {
            $registerFunction = 'registerTheme';
        } else if ($decoded['type'] == 'magento2-language') {
            $registerFunction = 'registerLanguage';
        }

        if ($registerFunction) {
            $phpContent = "<?php
    \\Magento\\Framework\\Module\\Registrar::" . $registerFunction . "('" . $moduleName . "', '" . $fileInfo->getRealPath() . "');
    ";
            file_put_contents($fileInfo->getRealPath() . "/module-registration.php", $phpContent);
        }
    }

}

    $file = 'vendor/composer/installed.json';
    $json = file_get_contents($file);
    $decoded = json_decode($json, true);

    foreach ($decoded as $num => $package) {
    if (isset($package['extra']['map'])) {
        $moduleName = str_replace('/', "_", $package['extra']['map'][0][1]);
        $classPath = str_replace('/', "\\", $package['extra']['map'][0][1]) . "\\";
        var_dump($package['extra']['map'][0][1], $package['type']);

        $registerFunction = null;
        if ($package['type'] == 'magento2-module') {
            $registerFunction = 'registerModule';
            if (!isset($package['autoload']['psr-4'])) {
                $decoded[$num]['autoload']['psr-4'] = [$classPath => ""];
            }
        } else if ($package['type'] == 'magento2-theme') {
            $registerFunction = 'registerTheme';
        } else if ($package['type'] == 'magento2-language') {
            $registerFunction = 'registerLanguage';
        }


        if ($registerFunction) {
            $phpContent = "<?php
    \\Magento\\Framework\\Module\\Registrar::" . $registerFunction . "('" . $moduleName . "', '" . $fileInfo->getRealPath() . "');
    ";


            $decoded[$num]['autoload']['files'] = ["module-registration.php"];


            //var_dump($fileInfo->getRealPath() . "/comp.json");
            //if ($registerFunction == 'registerModule')
            //    die();
        }
    }
    }
file_put_contents("vendor/composer/installed1.json", json_encode($decoded, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

//}

