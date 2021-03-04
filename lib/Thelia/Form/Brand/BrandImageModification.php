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

namespace Thelia\Form\Brand;

use Thelia\Form\Image\ImageModification;

/**
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class BrandImageModification extends ImageModification
{
    /**
     * @inheritdoc
     */
    public static function getName()
    {
        return 'thelia_brand_image_modification';
    }
}
