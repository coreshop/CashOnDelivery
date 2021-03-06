<?php
/**
 * CashOnDelivery
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action\Payment;

/**
 * Class CashOnDelivery_PaymentController
 */
class CashOnDelivery_PaymentController extends Payment
{
    /**
     * User accepted Bankwire Payment -> createOrder
     */
    public function paymentAction()
    {
        //DoPayment
        $this->session->order = $this->cart->createOrder(
            \CoreShop\Model\Order\State::getByIdentifier('COD'),
            $this->getModule(),
            0,
            $this->view->language
        );

        $this->redirect($this->getModule()->getConfirmationUrl());
    }

    /**
     * @return CashOnDelivery\Shop
     */
    public function getModule()
    {
        return parent::getModule();
    }
}
