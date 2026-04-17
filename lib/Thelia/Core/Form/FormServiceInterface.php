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

use Symfony\Component\Form\Form;

/**
 * Minimal contract consumed by front-office bundles (eg. Flexy) that need to
 * build a Thelia form by name.
 *
 * A no-op default implementation is registered in the core so the container
 * can build even when no form renderer module (eg. TwigEngine) is active.
 */
interface FormServiceInterface
{
    /**
     * Build and return the Symfony Form instance for the given Thelia form name.
     */
    public function getFormByName(?string $name, array $data = []): Form;
}
