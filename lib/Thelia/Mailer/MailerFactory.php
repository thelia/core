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

namespace Thelia\Mailer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\MailTransporterEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Customer;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\MessageQuery;

/**
 * Class MailerFactory.
 *
 * @author Manuel Raynaud <manu@raynaud.io>
 * @author Franck Allimant <franck@cqfdev.fr>
 */
class MailerFactory
{
    /**
     * @var \Swift_Mailer
     */
    protected $swiftMailer;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var ParserInterface */
    protected $parser;

    public function __construct(EventDispatcherInterface $dispatcher, ParserInterface $parser)
    {
        $this->dispatcher = $dispatcher;
        $this->parser = $parser;

        $transporterEvent = new MailTransporterEvent();
        $this->dispatcher->dispatch($transporterEvent, TheliaEvents::MAILTRANSPORTER_CONFIG);

        if ($transporterEvent->hasTransporter()) {
            $transporter = $transporterEvent->getTransporter();
        } else {
            if (ConfigQuery::isSmtpEnable()) {
                $transporter = $this->configureSmtp();
            } else {
                $transporter = \Swift_MailTransport::newInstance();
            }
        }

        $this->swiftMailer = new \Swift_Mailer($transporter);
    }

    private function configureSmtp()
    {
        $smtpTransporter = \Swift_SmtpTransport::newInstance(ConfigQuery::getSmtpHost(), ConfigQuery::getSmtpPort());

        if (ConfigQuery::getSmtpEncryption()) {
            $smtpTransporter->setEncryption(ConfigQuery::getSmtpEncryption());
        }
        if (ConfigQuery::getSmtpUsername()) {
            $smtpTransporter->setUsername(ConfigQuery::getSmtpUsername());
        }
        if (ConfigQuery::getSmtpPassword()) {
            $smtpTransporter->setPassword(ConfigQuery::getSmtpPassword());
        }
        if (ConfigQuery::getSmtpAuthMode()) {
            $smtpTransporter->setAuthMode(ConfigQuery::getSmtpAuthMode());
        }
        if (ConfigQuery::getSmtpTimeout()) {
            $smtpTransporter->setTimeout(ConfigQuery::getSmtpTimeout());
        }
        if (ConfigQuery::getSmtpSourceIp()) {
            $smtpTransporter->setSourceIp(ConfigQuery::getSmtpSourceIp());
        }

        return $smtpTransporter;
    }

    /**
     * @param null $failedRecipients
     *
     * @return int number of recipients who were accepted for delivery
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        return $this->swiftMailer->send($message, $failedRecipients);
    }

    /**
     * @return \Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swiftMailer;
    }

    /**
     * Return a new message instance.
     *
     * @return \Swift_Message
     */
    public function getMessageInstance()
    {
        return \Swift_Message::newInstance();
    }

    /**
     * Send a message to the customer.
     *
     * @param string   $messageCode
     * @param Customer $customer
     * @param array    $messageParameters an array of (name => value) parameters that will be available in the message
     */
    public function sendEmailToCustomer($messageCode, $customer, $messageParameters = []): void
    {
        // Always add the customer ID to the parameters
        $messageParameters['customer_id'] = $customer->getId();

        $this->sendEmailMessage(
            $messageCode,
            [ConfigQuery::getStoreEmail() => ConfigQuery::getStoreName()],
            [$customer->getEmail() => $customer->getFirstname().' '.$customer->getLastname()],
            $messageParameters,
            $customer->getCustomerLang()->getLocale()
        );
    }

    /**
     * Send a message to the shop managers.
     *
     * @param string $messageCode
     * @param array  $messageParameters an array of (name => value) parameters that will be available in the message
     * @param array  $replyTo           Reply to addresses. An array of (email-address => name) [optional]
     */
    public function sendEmailToShopManagers($messageCode, $messageParameters = [], $replyTo = []): void
    {
        $storeName = ConfigQuery::getStoreName();

        // Build the list of email recipients
        $recipients = ConfigQuery::getNotificationEmailsList();

        $to = [];

        foreach ($recipients as $recipient) {
            $to[$recipient] = $storeName;
        }

        $this->sendEmailMessage(
            $messageCode,
            [ConfigQuery::getStoreEmail() => $storeName],
            $to,
            $messageParameters,
            null,
            [],
            [],
            $replyTo
        );
    }

    /**
     * Send a message to the customer.
     *
     * @param string $messageCode
     * @param array  $from              From addresses. An array of (email-address => name)
     * @param array  $to                To addresses. An array of (email-address => name)
     * @param array  $messageParameters an array of (name => value) parameters that will be available in the message
     * @param string $locale            if null, the default store locale is used
     * @param array  $cc                Cc addresses. An array of (email-address => name) [optional]
     * @param array  $bcc               Bcc addresses. An array of (email-address => name) [optional]
     * @param array  $replyTo           Reply to addresses. An array of (email-address => name) [optional]
     */
    public function sendEmailMessage($messageCode, $from, $to, $messageParameters = [], $locale = null, $cc = [], $bcc = [], $replyTo = []): void
    {
        $store_email = ConfigQuery::getStoreEmail();

        if (!empty($store_email)) {
            if (!empty($to)) {
                try {
                    $instance = $this->createEmailMessage($messageCode, $from, $to, $messageParameters, $locale, $cc, $bcc, $replyTo);

                    $sentCount = $this->send($instance, $failedRecipients);

                    if ($sentCount == 0) {
                        Tlog::getInstance()->addError(
                            Translator::getInstance()->trans(
                                'Failed to send message %code. Failed recipients: %failed_addresses',
                                [
                                    '%code' => $messageCode,
                                    '%failed_addresses' => \is_array($failedRecipients) ? implode(
                                        ',',
                                        $failedRecipients
                                    ) : 'none',
                                ]
                            )
                        );
                    }
                } catch (\Exception $ex) {
                    Tlog::getInstance()->addError(
                        "Error while sending email message $messageCode: ".$ex->getMessage()
                    );
                }
            } else {
                Tlog::getInstance()->addWarning("Message $messageCode not sent: recipient list is empty.");
            }
        } else {
            Tlog::getInstance()->addError("Can't send email message $messageCode: store email address is not defined.");
        }
    }

    /**
     * Create a SwiftMessage instance from a given message code.
     *
     * @param string $messageCode
     * @param array  $from              From addresses. An array of (email-address => name)
     * @param array  $to                To addresses. An array of (email-address => name)
     * @param array  $messageParameters an array of (name => value) parameters that will be available in the message
     * @param string $locale            if null, the default store locale is used
     * @param array  $cc                Cc addresses. An array of (email-address => name) [optional]
     * @param array  $bcc               Bcc addresses. An array of (email-address => name) [optional]
     * @param array  $replyTo           Reply to addresses. An array of (email-address => name) [optional]
     *
     * @return \Swift_Message the generated and built message
     *
     * @throws \Exception
     */
    public function createEmailMessage($messageCode, $from, $to, $messageParameters = [], $locale = null, $cc = [], $bcc = [], $replyTo = [])
    {
        if (null !== $message = MessageQuery::getFromName($messageCode)) {
            if ($locale === null) {
                $locale = Lang::getDefaultLanguage()->getLocale();
            }

            $message->setLocale($locale);

            // Assign parameters
            foreach ($messageParameters as $name => $value) {
                $this->parser->assign($name, $value);
            }

            // As the parser uses the lang stored in the session, temporarly set the required language into the session.
            // This is required in the back office when sending emails to customers, that may use a different locale than
            // the current one.
            $session = $this->parser->getRequest()->getSession();

            $currentLang = $session->getLang();

            if (null !== $requiredLang = LangQuery::create()->findOneByLocale($locale)) {
                $session->setLang($requiredLang);
            }

            $instance = $this->getMessageInstance();

            $this->setupMessageHeaders($instance, $from, $to, $cc, $bcc, $replyTo);

            $message->buildMessage($this->parser, $instance);

            $session->setLang($currentLang);

            return $instance;
        }

        throw new \RuntimeException(
            Translator::getInstance()->trans(
                "Failed to load message with code '%code%', propably because it does'nt exists.",
                ['%code%' => $messageCode]
            )
        );
    }

    /**
     * Create a SwiftMessage instance from text.
     *
     * @param array  $from     From addresses. An array of (email-address => name)
     * @param array  $to       To addresses. An array of (email-address => name)
     * @param string $subject  the message subject
     * @param string $htmlBody the HTML message body, or null
     * @param string $textBody the text message body, or null
     * @param array  $cc       Cc addresses. An array of (email-address => name) [optional]
     * @param array  $bcc      Bcc addresses. An array of (email-address => name) [optional]
     * @param array  $replyTo  Reply to addresses. An array of (email-address => name) [optional]
     *
     * @return \Swift_Message the generated and built message
     */
    public function createSimpleEmailMessage($from, $to, $subject, $htmlBody, $textBody, $cc = [], $bcc = [], $replyTo = [])
    {
        $instance = $this->getMessageInstance();

        $this->setupMessageHeaders($instance, $from, $to, $cc, $bcc, $replyTo);

        $instance->setSubject($subject);

        // If we do not have an HTML message
        if (empty($htmlBody)) {
            // Message body is the text message
            $instance->setBody($textBody, 'text/plain');
        } else {
            // The main body is the HTML messahe
            $instance->setBody($htmlBody, 'text/html');

            // Use the text as a message part, if we have one.
            if (!empty($textBody)) {
                $instance->addPart($textBody, 'text/plain');
            }
        }

        return $instance;
    }

    /**
     * @param array  $from             From addresses. An array of (email-address => name)
     * @param array  $to               To addresses. An array of (email-address => name)
     * @param string $subject          the message subject
     * @param string $htmlBody         the HTML message body, or null
     * @param string $textBody         the text message body, or null
     * @param array  $cc               Cc addresses. An array of (email-address => name) [optional]
     * @param array  $bcc              Bcc addresses. An array of (email-address => name) [optional]
     * @param array  $replyTo          Reply to addresses. An array of (email-address => name) [optional]
     * @param null   $failedRecipients The failed recipients list
     *
     * @return int number of recipients who were accepted for delivery
     */
    public function sendSimpleEmailMessage($from, $to, $subject, $htmlBody, $textBody, $cc = [], $bcc = [], $replyTo = [], &$failedRecipients = null)
    {
        $instance = $this->createSimpleEmailMessage($from, $to, $subject, $htmlBody, $textBody, $cc, $bcc, $replyTo);

        return $this->send($instance, $failedRecipients);
    }

    /**
     * @param \Swift_Message $instance
     * @param array          $from     From addresses. An array of (email-address => name)
     * @param array          $to       To addresses. An array of (email-address => name)
     * @param array          $cc       Cc addresses. An array of (email-address => name) [optional]
     * @param array          $bcc      Bcc addresses. An array of (email-address => name) [optional]
     * @param array          $replyTo  Reply to addresses. An array of (email-address => name) [optional]
     */
    protected function setupMessageHeaders($instance, $from, $to, $cc = [], $bcc = [], $replyTo = []): void
    {
        // Add from addresses
        foreach ($from as $address => $name) {
            $instance->addFrom($address, $name);
        }

        // Add to addresses
        foreach ($to as $address => $name) {
            $instance->addTo($address, $name);
        }

        // Add cc addresses
        foreach ($cc as $address => $name) {
            $instance->addCc($address, $name);
        }

        // Add bcc addresses
        foreach ($bcc as $address => $name) {
            $instance->addBcc($address, $name);
        }

        // Add reply to addresses
        foreach ($replyTo as $address => $name) {
            $instance->addReplyTo($address, $name);
        }
    }
}
