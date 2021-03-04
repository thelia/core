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

namespace Thelia\Core\Event\FeatureProduct;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\FeatureProduct;

/**
 * @deprecated since 2.4, please use \Thelia\Model\Event\FeatureProductEvent
 */
class FeatureProductEvent extends ActionEvent
{
    protected $featureProduct;

    public function __construct(FeatureProduct $featureProduct = null)
    {
        $this->featureProduct = $featureProduct;
    }

    public function hasFeatureProduct()
    {
        return null !== $this->featureProduct;
    }

    public function getFeatureProduct()
    {
        return $this->featureProduct;
    }

    public function setFeatureProduct($featureProduct)
    {
        $this->featureProduct = $featureProduct;

        return $this;
    }
}
