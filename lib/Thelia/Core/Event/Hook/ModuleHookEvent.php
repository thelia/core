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

namespace Thelia\Core\Event\Hook;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\ModuleHook;

/**
 * Class ModuleHookEvent
 * @package Thelia\Core\Event\Hook
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class ModuleHookEvent extends ActionEvent
{
    public $moduleHook;

    public function __construct(ModuleHook $moduleModuleHook = null)
    {
        $this->moduleHook = $moduleModuleHook;
    }

    public function hasModuleHook()
    {
        return ! \is_null($this->moduleHook);
    }

    public function getModuleHook()
    {
        return $this->moduleHook;
    }

    public function setModuleHook(ModuleHook $moduleHook)
    {
        $this->moduleHook = $moduleHook;

        return $this;
    }
}
