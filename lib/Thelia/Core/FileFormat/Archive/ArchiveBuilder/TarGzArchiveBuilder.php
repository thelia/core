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

namespace Thelia\Core\FileFormat\Archive\ArchiveBuilder;
use Thelia\Core\FileFormat\Archive\AbstractArchiveBuilder;

/**
 * Class TarGzArchiveBuilder
 * @package Thelia\Core\FileFormat\Archive\ArchiveBuilder
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class TarGzArchiveBuilder extends TarArchiveBuilder
{
    public function __construct()
    {
        if (!extension_loaded("zlib")) {
            return null;
        }

        parent::__construct();
    }


    public function getName()
    {
        return "tar.gz";
    }

    public function getMimeType()
    {
        return "application/x-gzip";
    }

    public function getExtension()
    {
        return "tgz";
    }

    protected function compressionEntryPoint()
    {
        if ($this->compression != \Phar::GZ) {
            $this->tar = $this->tar->compress(\Phar::GZ, $this->getExtension());
        }

        $this->compression = \Phar::GZ;

        return $this;
    }

}
