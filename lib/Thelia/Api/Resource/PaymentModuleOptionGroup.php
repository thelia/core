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
 * Aggregates {@see PaymentModuleOption} entries surfaced by listeners attached
 * to `TheliaEvents::MODULE_PAYMENT_GET_OPTIONS`. Returned to the client as
 * part of {@see PaymentModule::$optionGroups}.
 */
class PaymentModuleOptionGroup
{
    #[ApiProperty(description: 'Code of the payment module option group', required: true, example: 'paypal_type')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $code;

    #[ApiProperty(description: 'Title of the payment module option group', example: 'Choose a payment option')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $title;

    #[ApiProperty(description: 'Description of the payment module option group', example: '')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private ?string $description = null;

    #[ApiProperty(description: 'Minimum number of options that must be selected by the customer', example: 1)]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private ?int $minimumSelectedOptions = null;

    #[ApiProperty(description: 'Maximum number of options that can be selected by the customer', example: 1)]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private ?int $maximumSelectedOptions = null;

    /**
     * @var array<int, PaymentModuleOption>
     */
    #[ApiProperty(description: 'Options offered inside this group')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private array $options = [];

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMinimumSelectedOptions(): ?int
    {
        return $this->minimumSelectedOptions;
    }

    public function setMinimumSelectedOptions(?int $minimumSelectedOptions): self
    {
        $this->minimumSelectedOptions = $minimumSelectedOptions;

        return $this;
    }

    public function getMaximumSelectedOptions(): ?int
    {
        return $this->maximumSelectedOptions;
    }

    public function setMaximumSelectedOptions(?int $maximumSelectedOptions): self
    {
        $this->maximumSelectedOptions = $maximumSelectedOptions;

        return $this;
    }

    /**
     * @return array<int, PaymentModuleOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<int, PaymentModuleOption> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function appendPaymentModuleOption(PaymentModuleOption $option): self
    {
        $this->options[] = $option;

        return $this;
    }
}
