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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Type;
use Thelia\Core\Translation\Translator;

class MessageModificationForm extends BaseForm
{
    public function getName()
    {
        return "thelia_message_modification";
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add("id", HiddenType::class, array(
                "constraints" => array(
                    new GreaterThan(array('value' => 0))
                )
            ))

            ->add("name", TextType::class, array(
                "constraints" => array(new NotBlank()),
                "label" => Translator::getInstance()->trans('Name'),
                "label_attr" => array(
                    "for" => "name",
                    "help" => Translator::getInstance()->trans('This the unique name of this message. Do not change this value unless you understand what you do.'),
                ),
                'attr' => [
                    "placeholder" => Translator::getInstance()->trans('Message name'),
                ],
            ))
            // Define all messages as not secured.
            ->add("secured", HiddenType::class, array(
                "constraints" => array(new Type([ 'type' => 'bool'])),
                'required' => false,
                'data' => false,
            ))
            ->add("locale", HiddenType::class, array())
            ->add("title", TextType::class, array(
                "constraints" => array(new NotBlank()),
                "label" => Translator::getInstance()->trans('Title'),
                "label_attr" => array(
                    "for" => "title",
                    "help" => Translator::getInstance()->trans("This is the message purpose, such as 'Order confirmation'."),
                ),
                'attr' => [
                    "placeholder" => Translator::getInstance()->trans('Title'),
                ],
            ))
            ->add("subject", TextType::class, array(
                "constraints" => array(new NotBlank()),
                "label" => Translator::getInstance()->trans('Message subject'),
                "label_attr" => array(
                    "for" => "subject",
                    "help" => Translator::getInstance()->trans("This is the subject of the e-mail, such as 'Your order is confirmed'."),
                ),
                'attr' => [
                    "placeholder" => Translator::getInstance()->trans('Message subject'),
                ],
            ))

            ->add("html_message", TextType::class, array(
                "label" => Translator::getInstance()->trans('HTML Message'),
                "label_attr" => array(
                    "for" => "html_message",
                    "help" => Translator::getInstance()->trans("The mailing template in HTML format."),
                ),
                "required" => false,
            ))

            ->add("text_message", TextareaType::class, array(
                "label" => Translator::getInstance()->trans('Text Message'),
                "label_attr" => array(
                    "for" => "text_message",
                    "help" => Translator::getInstance()->trans("The mailing template in text-only format."),
                ),
                'required' => false,
            ))

            ->add("html_layout_file_name", TextType::class, array(
                "label" => Translator::getInstance()->trans('Name of the HTML layout file'),
                "label_attr" => array(
                        "for" => "html_layout_file_name",
                ),
                "required" => false,
            ))

            ->add("html_template_file_name", TextType::class, array(
                "label" => Translator::getInstance()->trans('Name of the HTML template file'),
                "label_attr" => array(
                        "for" => "html_template_file_name",
                ),
                "required" => false,
            ))

            ->add("text_layout_file_name", TextType::class, array(
                "label" => Translator::getInstance()->trans('Name of the text layout file'),
                "label_attr" => array(
                    "for" => "text_layout_file_name",
                ),
                "required" => false,
            ))

            ->add("text_template_file_name", TextType::class, array(
                "label" => Translator::getInstance()->trans('Name of the text template file'),
                "label_attr" => array(
                        "for" => "text_template_file_name",
                ),
                "required" => false,
            ))
        ;
    }
}
