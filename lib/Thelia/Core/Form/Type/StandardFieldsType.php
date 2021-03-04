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

namespace Thelia\Core\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Thelia\Form\StandardDescriptionFieldsTrait;

/**
 * Class StandardFieldsType.
 *
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class StandardFieldsType extends AbstractType
{
    use StandardDescriptionFieldsTrait;
    protected $formBuilder;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addStandardDescFields();
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'standard_fields';
    }
}
