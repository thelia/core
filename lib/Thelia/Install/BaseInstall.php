<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
namespace Thelia\Install;
use Thelia\Install\Exception\AlreadyInstallException;

/**
 * Class BaseInstall
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
abstract class BaseInstall
{
    /**
     * Verify if an installation already exists
     */
    public function __construct($verifyInstall = true)
    {
        /* TODO : activate this part
        if (file_exists(THELIA_ROOT . '/local/config/database.yml') && $verifyInstall) {
            throw new AlreadyInstallException("Thelia is already installed");
        }*/


        $this->exec();
    }

    abstract public function exec();
}