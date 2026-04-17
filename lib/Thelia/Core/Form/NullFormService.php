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

namespace Thelia\Core\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Default no-op implementation used when no form renderer module
 * (eg. TwigEngine) is installed or active. Returns an empty form so
 * consumer components keep a valid Form instance and can render without
 * crashing the kernel; submitting such a form is a no-op.
 *
 * The interface alias pointing to this class is registered in
 * Config/Resources/services/core/front_fallbacks.php so active modules can
 * override it with `#[AsAlias(FormServiceInterface::class)]`.
 */
final class NullFormService implements FormServiceInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function getFormByName(?string $name, array $data = []): Form
    {
        return $this->formFactory->create(FormType::class, $data);
    }
}
