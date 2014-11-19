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

namespace Thelia\Action;

use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\CustomerTitle\CustomerTitleEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\CustomerTitle as CustomerTitleModel;
use Thelia\Model\Map\CustomerTitleTableMap;

/**
 * Class CustomerTitle
 * @package Thelia\Action
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class CustomerTitle extends BaseAction implements EventSubscriberInterface
{
    public function create(CustomerTitleEvent $event)
    {
        $this->createOrUpdate($event, new CustomerTitleModel());
    }

    public function update(CustomerTitleEvent $event)
    {
        $this->checkCustomerTitle($event);

        $this->createOrUpdate($event, $event->getCustomerTitle());
    }

    public function delete(CustomerTitleEvent $event)
    {
        $this->checkCustomerTitle($event);

        $con = Propel::getConnection(CustomerTitleTableMap::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $event->getCustomerTitle()->delete();

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }

        $event->setCustomerTitle(null);
    }

    protected function checkCustomerTitle(CustomerTitleEvent $event)
    {
        if (null === $event->getCustomerTitle()) {
            throw new \LogicException(
                "You must set the customer title before its update"
            );
        }
    }

    protected function createOrUpdate(CustomerTitleEvent $event, CustomerTitleModel $customerTitle)
    {
        $con = Propel::getConnection(CustomerTitleTableMap::DATABASE_NAME);
        $con->beginTransaction();

        $i18n  = $customerTitle->getTranslation($event->getLocale(), $con);

        try {
            $i18n
                ->setShort($event->getShort())
                ->setLong($event->getLong())
            ;

            $customerTitle->save($con);

            if ($event->isDefault()) {
                $customerTitle->toggleDefault($con);
                $event->setDefault(false);
            }

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }

        $event->setCustomerTitle($customerTitle);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::CUSTOMER_TITLE_CREATE => array("create"),
            TheliaEvents::CUSTOMER_TITLE_UPDATE => array("update"),
            TheliaEvents::CUSTOMER_TITLE_DELETE => array("delete"),
        );
    }
}
