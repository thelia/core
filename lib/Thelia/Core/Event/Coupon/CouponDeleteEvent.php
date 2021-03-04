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

namespace Thelia\Core\Event\Coupon;

use Thelia\Core\Event\ActionEvent;
use Thelia\Model\Coupon;

/**
 * Class CouponDeleteEvent
 * @package Thelia\Core\Event\Coupon
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class CouponDeleteEvent extends ActionEvent
{
    /** @var Coupon */
    protected $coupon;

    protected $couponId;

    public function __construct($couponId, Coupon $coupon = null)
    {
        $this->coupon = $coupon;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     * @return $this
     */
    public function setCoupon(Coupon $coupon = null)
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     */
    public function getCouponId()
    {
        return $this->couponId;
    }

    /**
     * @return $this
     */
    public function setCouponId($couponId)
    {
        $this->couponId = $couponId;
        return $this;
    }
}
