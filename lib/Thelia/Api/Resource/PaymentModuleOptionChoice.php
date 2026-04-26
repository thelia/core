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
 * Customer-side selection inside a {@see PaymentModuleOptionGroup}. Each entry
 * lists the option codes (`values`) chosen by the customer for the named group.
 */
class PaymentModuleOptionChoice
{
    #[ApiProperty(description: 'Code of the targeted payment module option group', required: true)]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private string $group;

    /**
     * @var array<int, string>
     */
    #[ApiProperty(description: 'Selected option codes within the group')]
    #[Groups([PaymentModule::GROUP_FRONT_READ])]
    private array $values = [];

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<int, string> $values
     */
    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }
}
