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

namespace Thelia\Api\Bridge\Propel\Event;

use Thelia\Api\Resource\PaymentModuleOptionGroup;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\AddressQuery;
use Thelia\Model\Base\CountryQuery;
use Thelia\Model\Cart;
use Thelia\Model\Country;
use Thelia\Model\Module;
use Thelia\Model\State;

/**
 * Dispatched on `TheliaEvents::MODULE_PAYMENT_GET_OPTIONS` so payment module
 * listeners can publish their {@see PaymentModuleOptionGroup} payload for the
 * `/api/front/payment/modules` collection.
 */
class PaymentModuleOptionEvent extends ActionEvent
{
    /**
     * @var array<int, PaymentModuleOptionGroup>
     */
    protected array $paymentModuleOptionGroups = [];

    protected ?Address $address = null;

    protected ?Country $country = null;

    protected ?State $state = null;

    public function __construct(
        protected Module $module,
        protected ?Cart $cart = null,
    ) {
        if (!$module->isPayementModule()) {
            throw new \RuntimeException(Translator::getInstance()->trans($module->getTitle().' is not a payment module.'));
        }

        $addressId = $cart?->getCartAddressRelatedByAddressInvoiceId()?->getAddressId()
            ?? $cart?->getCartAddressRelatedByAddressDeliveryId()?->getAddressId();

        $this->address = null !== $addressId ? AddressQuery::create()->findPk($addressId) : null;
        $this->country = $this->address?->getCountry()
            ?? CountryQuery::create()->filterByByDefault(true)->findOne();
        $this->state = $this->address?->getState()
            ?? $this->country?->getStates()?->getFirst()
            ?? null;
    }

    /**
     * @return array<int, PaymentModuleOptionGroup>
     */
    public function getPaymentModuleOptionGroups(): array
    {
        return $this->paymentModuleOptionGroups;
    }

    /**
     * @param array<int, PaymentModuleOptionGroup> $paymentModuleOptionGroups
     */
    public function setPaymentModuleOptionGroups(array $paymentModuleOptionGroups): static
    {
        $this->paymentModuleOptionGroups = $paymentModuleOptionGroups;

        return $this;
    }

    public function appendPaymentModuleOptionGroups(PaymentModuleOptionGroup $paymentModuleOptionGroup): static
    {
        $this->paymentModuleOptionGroups[] = $paymentModuleOptionGroup;

        return $this;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function setModule(Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }
}
