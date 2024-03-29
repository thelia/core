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

use Thelia\Core\Translation\Translator;
use Thelia\Model\CartItem;

/**
 * A trait to manage a coupon which removes a constant amount from the order total.
 * Should be used on coupons classes which implements AmountAndPercentageCouponInterface.
 *
 * Class AmountCouponTrait
 *
 * @author Franck Allimant <franck@cqfdev.fr>
 */
trait AmountCouponTrait
{
    // The amount is already defined in CouponAbstract, and should not be redefined here.
    // protected $amount = 0;

    /**
     * Should return the amount field name, defined in the parent class.
     *
     * @return string the percentage field name
     */
    abstract protected function getAmountFieldName();

    public function setFieldsValue($effects): void
    {
        $this->amount = $effects[$this->getAmountFieldName()];
    }

    public function getCartItemDiscount(CartItem $cartItem)
    {
        return $cartItem->getQuantity() * $this->amount;
    }

    public function callDrawBackOfficeInputs($templateName)
    {
        return $this->drawBaseBackOfficeInputs($templateName, [
                'amount_field_name' => $this->makeCouponFieldName($this->getAmountFieldName()),
                'amount_value' => $this->amount,
            ]);
    }

    protected function getFieldList()
    {
        return $this->getBaseFieldList([$this->getAmountFieldName()]);
    }

    protected function checkCouponFieldValue($fieldName, $fieldValue)
    {
        $this->checkBaseCouponFieldValue($fieldName, $fieldValue);

        if ($fieldName === $this->getAmountFieldName()) {
            if ((float) $fieldValue < 0) {
                throw new \InvalidArgumentException(
                    Translator::getInstance()->trans(
                        'Value %val for Discount Amount is invalid. Please enter a positive value.',
                        ['%val' => $fieldValue]
                    )
                );
            }
        }

        return $fieldValue;
    }
}
