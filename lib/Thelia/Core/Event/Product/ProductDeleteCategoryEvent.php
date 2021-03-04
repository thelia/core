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

namespace Thelia\Core\Event\Product;

use Thelia\Model\Product;

class ProductDeleteCategoryEvent extends ProductEvent
{
    protected $category_id;

    public function __construct(Product $product, $category_id)
    {
        parent::__construct($product);

        $this->category_id = $category_id;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }
}
