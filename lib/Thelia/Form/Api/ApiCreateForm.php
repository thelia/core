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

namespace Thelia\Form\Api;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ProfileQuery;

/**
 * Class ApiCreateForm
 * @package Thelia\Form\Api
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class ApiCreateForm extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", TextType::class)
     *   ->add("email", EmailType::class, array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', IntegerType::class);
     *
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('label', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => Translator::getInstance()->trans('label'),
                'label_attr' => ['for' => 'api_label']
            ])
            ->add(
                'profile',
                ChoiceType::class,
                array(
                    "choices" => ProfileQuery::getProfileList(),
                    "constraints" => array(
                        new NotBlank(),
                    ),
                    "label" => Translator::getInstance()->trans('Profile'),
                    "label_attr" => array(
                        "for" => "profile",
                    ),
                )
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'thelia_api_create';
    }
}
