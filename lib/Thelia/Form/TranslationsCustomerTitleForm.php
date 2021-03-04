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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CustomerTitleQuery;


class TranslationsCustomerTitleForm extends BaseForm
{
    public function buildForm()
    {

        $this->formBuilder->add("locale", TextType::class, array(
            "required" => true,
            "constraints" => array(
                new NotBlank(),
            ),
        ));

        $allTitle = CustomerTitleQuery::create()->find();

        foreach($allTitle as $aTitle)
        {
            $id = $aTitle->getId();
            $this->formBuilder
                ->add("title_id_".$id,HiddenType::class, array(
                    "required" => true,
                    "constraints" => array(
                        new GreaterThan(array('value' => 0))
                    ),
                    "data" => $id
                ))
                ->add("short_title_".$id,TextType::class, array(
                    "label" => Translator::getInstance()->trans("Change short title for"),
                    "required" => true,
                    "constraints" => array(
                        new NotBlank()
                    )
                ))
                ->add("long_title_".$id,TextType::class, array(
                    "label" => Translator::getInstance()->trans("Change long title for"),
                    "required" => true,
                    "constraints" => array(
                        new NotBlank()
                    )
                ));
        }
    }
    public static function getName()
    {
        return 'thelia_translation_customer_title';
    }
}
