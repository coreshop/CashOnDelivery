<?php

use CoreShop\Controller\Action\Payment;
use Pimcore\Model\Object\CoreShopPayment;

use CoreShop\Tool;

class CoreShopCod_PaymentController extends Payment
{
    /**
     * User accepted Bankwire Payment -> createOrder
     */
    public function paymentAction()
    {
        //DoPayment
        $this->session->order = $this->getModule()->createOrder($this->cart, \CoreShop\Model\OrderState::getById(\CoreShop\Config::getValue("ORDERSTATE.COD")), $this->cart->getTotal(), $this->view->language);

        $this->redirect($this->getModule()->getConfirmationUrl());
    }

    /**
     * @return CoreShopCod\Shop
     */
    public function getModule()
    {
        return parent::getModule();
    }
}
