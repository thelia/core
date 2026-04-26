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

namespace Thelia\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Data carrier surfaced by `PaymentModuleService` on
 * `GET /api/front/payment/modules`. Populated by listeners attached to
 * `TheliaEvents::MODULE_PAYMENT_GET_OPTIONS` and aggregated through
 * {@see PaymentModuleOptionGroup}.
 */
class PaymentModuleOption
{
    #[ApiProperty(description: 'Code of the payment module option', required: true, example: 'paypal')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $code;

    #[ApiProperty(description: 'Validity of the payment module option', example: true)]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private bool $valid = true;

    #[ApiProperty(description: 'Title of the payment module option', example: 'Pay with Paypal')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $title;

    #[ApiProperty(description: 'Description of the payment module option', example: 'Direct payment via Paypal')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $description = '';

    #[ApiProperty(description: 'URL of the option logo', example: 'https://example.com/logo.png')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $image = '';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
