<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Created by JetBrains PhpStorm.
 * Date: 8/29/13
 * Time: 3:45 PM
 *
 * Allow to build a form Install Step 3 Database connection
 *
 * @package Coupon
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 *
 */
class InstallStep3Form extends BaseForm
{
    /**
     * Build Coupon form
     *
     * @return void
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'host',
                TextType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add(
                'user',
                TextType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add(
                'password',
                TextType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add(
                'port',
                TextType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                        new GreaterThan(
                            array(
                                'value' => 0,
                            )
                        ),
                    ),
                )
            )
            ->add(
                'locale',
                HiddenType::class,
                array(
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            );
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'thelia_install_step3';
    }
}
