<?php
/**
 * Encapsulates directories structure of a Magento module
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Module;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class ThemeDir
{
    /**
     * Module registry
     *
     * @var ModuleRegistryInterface
     */
    private $moduleRegistry;

    private $modulesDirectory;


    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        ModuleRegistryInterface $moduleRegistry
    ) {
        $this->moduleRegistry = $moduleRegistry;
        $this->modulesDirectory = $filesystem->getDirectoryRead(DirectoryList::MODULES);
    }


    public function search($pattern, $path = null)
    {
        $result = [];
        foreach ($this->moduleRegistry->getThemesPaths() as $path) {
            $result = array_merge($result, $this->modulesDirectory->search($pattern, $path));
        }
        return $result;
    }

    public function getAreaConfiguration($path)
    {
        foreach ($this->moduleRegistry->getThemesPaths() as $key => $themePath) {
            if (strpos($themePath, $path) !== FALSE) {
                $pathPieces = explode('/', $key);
                $area = array_shift($pathPieces);
                return ['area' => $area, 'theme_path_pieces' => $pathPieces];
            }
        }
    }

    public function getPathByKey($key)
    {
        $themePaths = $this->moduleRegistry->getThemesPaths();
        return isset($themePaths[$key]) ? $themePaths[$key] : null;
    }
}
