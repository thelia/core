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

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Product;

class ProductCloneEvent extends ActionEvent
{
    /** @var string */
    protected $ref;
    /** @var string */
    protected $lang;
    /** @var Product */
    protected $originalProduct;
    /** @var Product */
    protected $clonedProduct;
    /** @var array */
    protected $types = ['images', 'documents'];

    /**
     * ProductCloneEvent constructor.
     *
     * @param string $lang the locale (such as fr_FR)
     */
    public function __construct(
        string $ref,
        string $lang,
        Product $originalProduct
    ) {
        $this->ref = $ref;
        $this->lang = $lang;
        $this->originalProduct = $originalProduct;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    public function setRef(string $ref): void
    {
        $this->ref = $ref;
    }

    /**
     * @return string the locale (such as fr_FR)
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang the locale (such as fr_FR)
     */
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    /**
     * @return Product
     */
    public function getOriginalProduct()
    {
        return $this->originalProduct;
    }

    /**
     * @param Product $originalProduct
     */
    public function setOriginalProduct($originalProduct): void
    {
        $this->originalProduct = $originalProduct;
    }

    /**
     * @return Product
     */
    public function getClonedProduct()
    {
        return $this->clonedProduct;
    }

    /**
     * @param Product $clonedProduct
     */
    public function setClonedProduct($clonedProduct): void
    {
        $this->clonedProduct = $clonedProduct;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }
}
