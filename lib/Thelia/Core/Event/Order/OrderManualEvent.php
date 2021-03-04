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

namespace Thelia\Core\Event\Order;

use Thelia\Model\Cart;
use Thelia\Model\Currency;
use Thelia\Model\Customer;
use Thelia\Model\Lang;
use Thelia\Model\Order;

class OrderManualEvent extends OrderEvent
{
    protected $currency;
    protected $lang;
    protected $cart;
    protected $customer;
    protected $useOrderDefinedAddresses = false;

    /**
     */
    public function __construct(Order $order, Currency $currency, Lang $lang, Cart $cart, Customer $customer)
    {
        parent::__construct($order);

        $this
            ->setCurrency($currency)
            ->setLang($lang)
            ->setCart($cart)
            ->setCustomer($customer)
        ;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     * @return $this
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     * @return $this
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseOrderDefinedAddresses()
    {
        return $this->useOrderDefinedAddresses;
    }

    /**
     * If true, the order will be created using the delivery and invoice addresses defined in $this->order instead of
     * creating new OrderAdresses using the Order::getChoosenXXXAddress().
     *
     * @param boolean $useOrderDefinedAddresses
     * @return $this
     */
    public function setUseOrderDefinedAddresses($useOrderDefinedAddresses)
    {
        $this->useOrderDefinedAddresses = $useOrderDefinedAddresses;
        return $this;
    }
}
