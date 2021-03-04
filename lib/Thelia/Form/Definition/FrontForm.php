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

namespace Thelia\Form\Definition;

/**
 * Class FrontForm
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 * @package Thelia\Form\Definition
 */
final class FrontForm
{
    const ADDRESS_CREATE = 'thelia.front.address.create';
    const ADDRESS_UPDATE = 'thelia.front.address.update';
    const CART_ADD = 'thelia.cart.add';
    const CONTACT = 'thelia.front.contact';
    const COUPON_CONSUME = 'thelia.order.coupon';
    const CUSTOMER_LOGIN = 'thelia.front.customer.login';
    const CUSTOMER_LOST_PASSWORD = 'thelia.front.customer.lostpassword';
    const CUSTOMER_CREATE = 'thelia.front.customer.create';
    const CUSTOMER_PROFILE_UPDATE = 'thelia.front.customer.profile.update';
    const CUSTOMER_PASSWORD_UPDATE = 'thelia.front.customer.password.update';
    const NEWSLETTER = 'thelia.front.newsletter';
    const NEWSLETTER_UNSUBSCRIBE = 'thelia.front.newsletter.unsubscribe';
    const ORDER_DELIVER = 'thelia.order.delivery';
    const ORDER_PAYMENT = 'thelia.order.payment';
}
