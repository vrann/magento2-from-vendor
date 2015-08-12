<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Router\ActionList;

class Reader
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    private $registrar;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Module\Registrar $registrar
    ) {
        $this->moduleReader = $moduleReader;
        $this->registrar = $registrar;
    }

    /**
     * Read list of all available application actions
     *
     * @return array
     */
    public function read()
    {
        $actionFiles = $this->moduleReader->getActionFiles();

        return $actionFiles;
    }
}
