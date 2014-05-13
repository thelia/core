<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Core\Event\Hook;
use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Hook;


/**
 * Class HookEvent
 * @package Thelia\Core\Event\Hook
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class HookEvent extends ActionEvent {

    public $hook = null;

    public function __construct(Hook $hook = null)
    {
        $this->hook = $hook;
    }

    public function hasModuleHook()
    {
        return ! is_null($this->hook);
    }

    public function getModuleHook()
    {
        return $this->hook;
    }

    public function setModuleHook(Hook $hook)
    {
        $this->hook = $hook;

        return $this;
    }


} 