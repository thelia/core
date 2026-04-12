<?php

declare(strict_types=1);

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

class ProductCombinationGenerationEvent extends ProductEvent
{
    protected string $reference = '';
    protected float $price = 0;
    protected int $currency_id;
    protected float $weight = 0;
    protected float $quantity = 0;
    protected float $sale_price = 0;
    protected bool $onsale = false;
    protected bool $isnew = false;
    protected string $ean_code = '';
    protected array $combinations;

    public function __construct(Product $product, int $currency_id, array $combinations)
    {
        parent::__construct($product);

        $this->setCombinations($combinations);
        $this->setCurrencyId($currency_id);
    }

    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function setCurrencyId(int $currency_id): static
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSalePrice(): float
    {
        return $this->sale_price;
    }

    public function setSalePrice(float $sale_price): static
    {
        $this->sale_price = $sale_price;

        return $this;
    }

    public function getOnsale(): bool
    {
        return $this->onsale;
    }

    public function setOnsale(bool $onsale): static
    {
        $this->onsale = $onsale;

        return $this;
    }

    public function getIsnew(): bool
    {
        return $this->isnew;
    }

    public function setIsnew(bool $isnew): static
    {
        $this->isnew = $isnew;

        return $this;
    }

    public function getEanCode(): string
    {
        return $this->ean_code;
    }

    public function setEanCode(string $ean_code): static
    {
        $this->ean_code = $ean_code;

        return $this;
    }

    public function getCombinations(): array
    {
        return $this->combinations;
    }

    public function setCombinations(array $combinations): static
    {
        $this->combinations = $combinations;

        return $this;
    }
}
