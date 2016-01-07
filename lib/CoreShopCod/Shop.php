<?php

namespace CoreShopCod;

use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Plugin\Payment as CorePayment;
use CoreShop\Plugin as CorePlugin;
use CoreShop\Tool;

class Shop extends CorePayment
{
    public static $install;

    /**
     * Attach Events for CoreShop
     *
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachEvents()
    {
        CorePlugin::getEventManager()->attach("payment.getProvider", function($e) {
            return $this;
        });

        CorePlugin::getEventManager()->attach('controller.init', function($e) {
            $controller = $e->getTarget();
            
            $controller->view->setScriptPath(
                array_merge(
                    $controller->view->getScriptPaths(),
                    array(
                        PIMCORE_PLUGINS_PATH . '/CoreShopCod/views/scripts/',
                        CORESHOP_TEMPLATE_PATH . '/views/scripts/coreshopcod/'
                    )
                )
            );
        });
    }

    /**
     * Get Payment Provider Name
     *
     * @return string
     */
    public function getName()
    {
        return "Cash on Delivery";
    }

    /**
     * Get Payment Provider Description
     *
     * @return string
     */
    public function getDescription()
    {
        return "";
    }

    /**
     * Get Payment Provider Image
     *
     * @return string
     */
    public function getImage()
    {
        return "/plugins/CoreShopCod/static/img/cod.gif";
    }

    /**
     * Get Payment Provider Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return "payment_cod";
    }

    /**
     * Get Payment Fee
     *
     * @param Cart $cart
     * @return int
     */
    public function getPaymentFee(Cart $cart)
    {
        return 0;
    }

    /**
     * Process CoreShop Payment
     *
     * @param Order $order
     * @return string
     */
    public function processPayment(Order $order)
    {
        $coreShopPayment = $order->createPayment($this, $order->getTotal());

        $this->validateOrder($coreShopPayment, $order, \CoreShop\Model\OrderState::getById(\CoreShop\Config::getValue("ORDERSTATE.COD")));

        Tool::prepareCart();
        $session = Tool::getSession();

        unset($session->order);
        unset($session->cart);
        unset($session->cartId);

        return "coreshopcod/cod";
    }
}