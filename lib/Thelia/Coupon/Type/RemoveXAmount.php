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

namespace Thelia\Coupon\Type;

/**
 * Allow to remove an amount from the checkout total.
 *
 * @author  Guillaume MOREL <gmorel@openstudio.fr>, Franck Allimant <franck@cqfdev.fr>
 */
class RemoveXAmount extends AbstractRemove
{
    use AmountCouponTrait;

    /** @var string Service Id */
    protected $serviceId = 'thelia.coupon.type.remove_x_amount';

    protected function getAmountFieldName()
    {
        return self::AMOUNT_FIELD_NAME;
    }

    public function getName()
    {
        return $this->facade
            ->getTranslator()
            ->trans('Fixed Amount Discount', []);
    }

    public function getToolTip()
    {
        $toolTip = $this->facade
            ->getTranslator()
            ->trans(
                'This coupon will subtracts a set amount from the total cost of an order. If the discount is greater than the total order corst, the customer will only pay the shipping, or nothing if the coupon also provides free shipping.',
                []
            );

        return $toolTip;
    }

    public function exec()
    {
        $cartTotal = $this->facade->getCartTotalTaxPrice($this->isAvailableOnSpecialOffers());

        if ($this->amount > $cartTotal) {
            return $cartTotal;
        }

        return $this->amount;
    }

    public function drawBackOfficeInputs()
    {
        return $this->callDrawBackOfficeInputs('coupon/type-fragments/remove-x-amount.html');
    }
}
