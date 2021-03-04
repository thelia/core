<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Event\Module;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Module;
use Thelia\Module\Validator\ModuleDefinition;

/**
 * Class ModuleEvent.
 *
 * @author  Manuel Raynaud <manu@raynaud.io>
 */
class ModuleInstallEvent extends ActionEvent
{
    /**
     * @var \Thelia\Model\Module
     */
    protected $module;

    /** @var ModuleDefinition $moduleDefinition */
    protected $moduleDefinition;

    /** @var string $modulePath */
    protected $modulePath;

    public function __construct(Module $module = null)
    {
        $this->module = $module;
    }

    public function setModule(Module $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function hasModule(): bool
    {
        return null !== $this->module;
    }

    /**
     * @param ModuleDefinition $moduleDefinition
     *
     * @return $this
     */
    public function setModuleDefinition($moduleDefinition): self
    {
        $this->moduleDefinition = $moduleDefinition;

        return $this;
    }

    /**
     * @return ModuleDefinition
     */
    public function getModuleDefinition()
    {
        return $this->moduleDefinition;
    }

    /**
     * @param string $modulePath
     *
     * @return $this
     */
    public function setModulePath($modulePath): self
    {
        $this->modulePath = $modulePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getModulePath()
    {
        return $this->modulePath;
    }
}
