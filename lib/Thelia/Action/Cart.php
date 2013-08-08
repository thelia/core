<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Thelia\Action;

use Propel\Runtime\Exception\PropelException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Event\CartEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Form\CartAdd;
use Thelia\Model\ProductPrice;
use Thelia\Model\ProductPriceQuery;
use Thelia\Model\CartItem;
use Thelia\Model\CartItemQuery;
use Thelia\Model\CartQuery;
use Thelia\Model\Cart as CartModel;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Customer;

/**
 *
 * Class Cart where all actions are manage like adding, modifying or delete items.
 *
 * Class Cart
 * @package Thelia\Action
 */
class Cart extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     *
     * add an article to cart
     *
     * @param \Thelia\Core\Event\ActionEvent $event
     */
    public function addArticle(ActionEvent $event)
    {
    	$request = $event->getRequest();

    	try {
    		$cartAdd = $this->getAddCartForm($request);

    		$form = $this->validateForm($cartAdd);

            $cart = $this->getCart($request);
            $newness = $form->get("newness")->getData();
            $append = $form->get("append")->getData();
            $quantity = $form->get("quantity")->getData();

            $productSaleElementsId = $form->get("product_sale_elements_id")->getData();
            $productId = $form->get("product")->getData();

            $cartItem = $this->findItem($cart->getId(), $productId, $productSaleElementsId);

            if($cartItem === null || $newness)
            {
                $productPrice = ProductPriceQuery::create()
                    ->filterByProductSaleElementsId($productSaleElementsId)
                    ->findOne()
                ;

                $this->addItem($cart, $productId, $productSaleElementsId, $quantity, $productPrice);
            }

            if($append && $cartItem !== null) {
                $this->updateQuantity($cartItem, $quantity);
            }


            $this->redirect($cartAdd->getSuccessUrl($request->getUriAddingParameters(array("addCart" => 1))));
        } catch (PropelException $e) {
            \Thelia\Log\Tlog::getInstance()->error(sprintf("Failed to add item to cart with message : %s", $e->getMessage()));
            $message = "Failed to add this article to your cart, please try again";
        }
        catch(FormValidationException $e) {

        	$message = $e->getMessage();
        }

        // The form has errors, propagate it.
        $this->propagateFormError($cartAdd, $message, $event);
    }

    protected function updateQuantity(CartItem $cartItem, $quantity)
    {
        $cartItem->setDisptacher($this->dispatcher);
        $cartItem->addQuantity($quantity)
            ->save();
    }

    protected function addItem(\Thelia\Model\Cart $cart, $productId, $productSaleElementsId, $quantity, ProductPrice $productPrice)
    {
        $cartItem = new CartItem();
        $cartItem->setDisptacher($this->dispatcher);
        $cartItem
            ->setCart($cart)
            ->setProductId($productId)
            ->setProductSaleElementsId($productSaleElementsId)
            ->setQuantity($quantity)
            ->setPrice($productPrice->getPrice())
            ->setPromoPrice($productPrice->getPromoPrice())
            ->setPriceEndOfLife(time() + ConfigQuery::read("cart.priceEOF", 60*60*24*30))
            ->save();
    }

    protected function findItem($cartId, $productId, $productSaleElementsId)
    {
        return CartItemQuery::create()
            ->filterByCartId($cartId)
            ->filterByProductId($productId)
            ->filterByProductSaleElementsId($productSaleElementsId)
            ->findOne();
    }

    private function getAddCartForm(Request $request)
    {
        if ($request->isMethod("post")) {
            $cartAdd = new CartAdd($request);
        } else {
            $cartAdd = new CartAdd(
                $request,
                "form",
                array(),
                array(
                    'csrf_protection'   => false,
                )
            );
        }

        return $cartAdd;
    }


    /**
     *
     * Delete specify article present into cart
     *
     * @param \Thelia\Core\Event\ActionEvent $event
     */
    public function deleteArticle(ActionEvent $event)
    {
        $request = $event->getRequest();

        if (null !== $cartItem = $request->get('cartItem')) {

        }
    }

    /**
     *
     * Modify article's quantity
     *
     * don't use Form here just test the Request.
     *
     * @param \Thelia\Core\Event\ActionEvent $event
     */
    public function modifyArticle(ActionEvent $event)
    {
        $request = $event->getRequest();

        $message = "";

        if(null !== $cartItemId = $request->get("cartItem") && null !== $quantity = $request->get("quantity")) {
            $cart = $this->getCart($request);
            $cartItem = CartItemQuery::create()
                ->filterByCartId($cart->getId())
                ->filterById($cartItemId)
                ->findOne();

            if($cartItem) {
                $this->updateQuantity($cartItem, $quantity);
            }
        }
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
            "action.addArticle" => array("addArticle", 128),
            "action.deleteArticle" => array("deleteArticle", 128),
            "action.modifyArticle" => array("modifyArticle", 128),
        );
    }

    /**
     *
     * search if cart already exists in session. If not try to create a new one or duplicate an old one.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Thelia\Model\Cart
     */
    public function getCart(Request $request)
    {

        if(null !== $cart = $request->getSession()->getCart()){
            return $cart;
        }

        if ($request->cookies->has("thelia_cart")) {
            //le cookie de panier existe, on le récupère
            $token = $request->cookies->get("thelia_cart");

            $cart = CartQuery::create()->findOneByToken($token);

            if ($cart) {
                //le panier existe en base
                $customer = $request->getSession()->getCustomerUser();

                if ($customer) {
                    if($cart->getCustomerId() != $customer->getId()) {
                        //le customer du panier n'est pas le mm que celui connecté, il faut cloner le panier sans le customer_id
                        $cart = $this->duplicateCart($cart, $request->getSession(), $customer);
                    }
                } else {
                    if ($cart->getCustomerId() != null) {
                        //il faut dupliquer le panier sans le customer_id
                        $cart = $this->duplicateCart($cart, $request->getSession());
                    }
                }

            } else {
                $cart = $this->createCart($request->getSession());
            }
        } else {
            //le cookie de panier n'existe pas, il va falloir le créer et faire un enregistrement en base.
            $cart = $this->createCart($request->getSession());
        }

        return $cart;
    }

    /**
     * @param \Thelia\Core\HttpFoundation\Session\Session $session
     * @return \Thelia\Model\Cart
     */
    protected function createCart(Session $session)
    {
        $cart = new CartModel();
        $cart->setToken($this->generateCookie());

        if(null !== $customer = $session->getCustomerUser()) {
            $cart->setCustomer($customer);
        }

        $cart->save();

        $session->setCart($cart->getId());

        return $cart;
    }


    /**
     * try to duplicate existing Cart. Customer is here to determine if this cart belong to him.
     *
     * @param \Thelia\Model\Cart $cart
     * @param \Thelia\Core\HttpFoundation\Session\Session $session
     * @param \Thelia\Model\Customer $customer
     * @return \Thelia\Model\Cart
     */
    protected function duplicateCart(CartModel $cart, Session $session, Customer $customer = null)
    {
        $newCart = $cart->duplicate($this->generateCookie(), $customer);
        $session->setCart($newCart->getId());

        $cartEvent = new CartEvent($newCart);
        $this->dispatcher->dispatch(TheliaEvents::CART_DUPLICATE, $cartEvent);

        return $cartEvent->cart;
    }

    protected function generateCookie()
    {
        $id = null;
        if (ConfigQuery::read("cart.session_only", 0) == 0) {
            $id = uniqid('', true);
            setcookie("thelia_cart", $id, time()+ConfigQuery::read("cart.cookie_lifetime", 60*60*24*365));

        }

        return $id;

    }
}
