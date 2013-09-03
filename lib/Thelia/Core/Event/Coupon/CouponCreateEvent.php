<?php
/**********************************************************************************/
/*                                                                                */
/*      Thelia	                                                                  */
/*                                                                                */
/*      Copyright (c) OpenStudio                                                  */
/*      email : info@thelia.net                                                   */
/*      web : http://www.thelia.net                                               */
/*                                                                                */
/*      This program is free software; you can redistribute it and/or modify      */
/*      it under the terms of the GNU General Public License as published by      */
/*      the Free Software Foundation; either version 3 of the License             */
/*                                                                                */
/*      This program is distributed in the hope that it will be useful,           */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of            */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             */
/*      GNU General Public License for more details.                              */
/*                                                                                */
/*      You should have received a copy of the GNU General Public License         */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.      */
/*                                                                                */
/**********************************************************************************/

namespace Thelia\Core\Event\Coupon;
use Thelia\Core\Event\ActionEvent;
use Thelia\Coupon\CouponRuleCollection;

/**
 * Created by JetBrains PhpStorm.
 * Date: 8/29/13
 * Time: 3:45 PM
 *
 * Occurring when a Coupon is created
 *
 * @package Coupon
 * @author  Guillaume MOREL <gmorel@openstudio.fr>
 *
 */
class CouponCreateEvent extends ActionEvent
{
    /** @var CouponRuleCollection Array of CouponRuleInterface */
    protected $rules = null;

    /** @var string Coupon code (ex: XMAS) */
    protected $code = null;

    /** @var string Coupon title (ex: Coupon for XMAS) */
    protected $title = null;

    /** @var string Coupon short description */
    protected $shortDescription = null;

    /** @var string Coupon description */
    protected $description = null;

    /** @var bool if Coupon is enabled */
    protected $isEnabled = false;

    /** @var \DateTime Coupon expiration date */
    protected $expirationDate = null;

    /** @var bool if Coupon is cumulative */
    protected $isCumulative = false;

    /** @var bool if Coupon is removing postage */
    protected $isRemovingPostage = false;

    /** @var float Amount that will be removed from the Checkout (Coupon Effect)  */
    protected $amount = 0;

    /** @var int Max time a Coupon can be used (-1 = unlimited) */
    protected $maxUsage = -1;

    /** @var bool if Coupon is available for Products already on special offers */
    protected $isAvailableOnSpecialOffers = false;

    /**
     * Return Coupon code (ex: XMAS)
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return Coupon title (ex: Coupon for XMAS)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return Coupon short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Return Coupon description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * If Coupon is cumulative or prevent any accumulation
     * If is cumulative you can sum Coupon effects
     * If not cancel all other Coupon and take the last given
     *
     * @return bool
     */
    public function isCumulative()
    {
        return $this->isCumulative;
    }

    /**
     * If Coupon is removing Checkout Postage
     *
     * @return bool
     */
    public function isRemovingPostage()
    {
        return $this->isRemovingPostage;
    }

    /**
     * Return effects generated by the coupon
     *
     * @return float Amount removed from the Total Checkout
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Return condition to validate the Coupon or not
     *
     * @return CouponRuleCollection
     */
    public function getRules()
    {
        return clone $this->rules;
    }

    /**
     * Set effects generated by the coupon
     *
     * @param float $amount Amount removed from the Total Checkout
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set Coupon Code
     *
     * @param string $code Coupon Code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set Coupon description
     *
     * @param string $description Coupon description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set Coupon expiration date (date given considered as expired)
     *
     * @param \DateTime $expirationDate Coupon expiration date
     *
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Return Coupon expiration date
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return clone $this->expirationDate;
    }

    /**
     * Set if Coupon is available on special offers
     *
     * @param boolean $isAvailableOnSpecialOffers is available on special offers
     *
     * @return $this
     */
    public function setIsAvailableOnSpecialOffers($isAvailableOnSpecialOffers)
    {
        $this->isAvailableOnSpecialOffers = $isAvailableOnSpecialOffers;

        return $this;
    }

    /**
     * If Coupon is available on special offers
     *
     * @return boolean
     */
    public function getIsAvailableOnSpecialOffers()
    {
        return $this->isAvailableOnSpecialOffers;
    }

    /**
     * Set if the Coupon is cumulative with other Coupons or not
     *
     * @param boolean $isCumulative is cumulative
     *
     * @return $this
     */
    public function setIsCumulative($isCumulative)
    {
        $this->isCumulative = $isCumulative;

        return $this;
    }

    /**
     * Enable/Disable the Coupon
     *
     * @param boolean $isEnabled Enable/Disable
     *
     * @return $this
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get if Coupon is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set if Coupon is removing Postage
     *
     * @param boolean $isRemovingPostage is removing Postage
     *
     * @return $this
     */
    public function setIsRemovingPostage($isRemovingPostage)
    {
        $this->isRemovingPostage = $isRemovingPostage;

        return $this;
    }

    /**
     * Set how many time a coupon can be used (-1 : unlimited)
     *
     * @param int $maxUsage Coupon quantity
     *
     * @return $this
     */
    public function setMaxUsage($maxUsage)
    {
        $this->maxUsage = $maxUsage;

        return $this;
    }

    /**
     * Return how many time the Coupon can be used again
     * Ex : -1 unlimited
     *
     * @return int
     */
    public function getMaxUsage()
    {
        return $this->maxUsage;
    }

    /**
     * Replace the existing Rules by those given in parameter
     * If one Rule is badly implemented, no Rule will be added
     *
     * @param CouponRuleCollection $rules CouponRuleInterface to add
     *
     * @return $this
     * @throws \Thelia\Exception\InvalidRuleException
     */
    public function setRules(CouponRuleCollection $rules)
    {
        $this->rules = $rules;
        $this->constraintManager = new ConstraintManager(
            $this->adapter,
            $this->rules
        );

        return $this;
    }

    /**
     * Set Coupon short description
     *
     * @param string $shortDescription Coupon short description
     *
     * @return $this
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Set Coupon title
     *
     * @param string $title Coupon title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

}
