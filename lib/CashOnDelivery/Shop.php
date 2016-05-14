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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CashOnDelivery;

use CoreShop\Model\Configuration;
use CoreShop\Model\Plugin\Payment as CorePayment;
use CoreShop\Plugin as CorePlugin;
use CoreShop\Tool;
use CoreShop\Model\Cart;
use Pimcore\Model\Tool\CustomReport\Config;

class Shop extends CorePayment
{
    /**
     * Attach Events for CoreShop
     *
     * @throws \Zend_EventManager_Exception_InvalidArgumentException
     */
    public function attachEvents()
    {
        \Pimcore::getEventManager()->attach("coreshop.payment.getProvider", function ($e) {
            return $this;
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
        return "/plugins/CashOnDelivery/static/img/cod.jpg";
    }

    /**
     * Get Payment Provider Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return "CashOnDelivery";
    }

    /**
     * @param Cart $cart
     * @returns boolean
     */
    public function isAvailable(Cart $cart)
    {
        $carrier = $cart->getCarrier();

        if (Configuration::get("COD.CARRIER.ACTIVE." . $carrier->getId())) {
            if($cart->getCustomerShippingAddress()) {
                $country = $cart->getCustomerShippingAddress()->getCountry();

                $availableCountries = Configuration::get("COD.CARRIER.COUNTRIES." . $carrier->getId());

                if(is_array($availableCountries)) {
                    foreach ($availableCountries as $countryId) {
                        if (intval($countryId) === intval($country->getId())) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get Payment Fee
     *
     * @param Cart $cart
     * @return int
     */
    public function getPaymentFee(Cart $cart, $useTaxes = true)
    {
        $fee = $this->getPaymentFeeForCart($cart);

        if($useTaxes) {
            $taxCalculator = $this->getTaxCalculator($cart);

            if ($taxCalculator) {
                return $taxCalculator->addTaxes($fee);
            }
        }

        return $fee;
    }

    /**
     * @param Cart $cart
     * @return bool|\CoreShop\Model\TaxCalculator
     * @throws \CoreShop\Exception\UnsupportedException
     */
    public function getTaxCalculator(Cart $cart) {
        $carrier = $cart->getCarrier();

        return $carrier->getTaxCalculator($cart->getCustomerAddressForTaxation());
    }

    /**
     * get payment fee
     *
     * @param Cart $cart
     * @return float
     */
    private function getPaymentFeeForCart(Cart $cart) {
        $carrier = $cart->getCarrier();

        if (Configuration::get("COD.CARRIER.PRICE." . $carrier->getId())) {
            return Configuration::get("COD.CARRIER.PRICE." . $carrier->getId());
        }

        return 0;
    }

    /**
     * Process Validation for Payment
     *
     * @param Cart $cart
     * @return mixed
     */
    public function process(Cart $cart)
    {
        return $this->getProcessValidationUrl();
    }

    public function processPaymentReturn()
    {
    }

    /**
     * Get url for confirmation link
     *
     * @return string
     */
    public function getConfirmationUrl()
    {
        return $this->url($this->getIdentifier(), 'confirmation');
    }

    /**
     * get url for validation link
     *
     * @return string
     */
    public function getProcessValidationUrl()
    {
        return $this->url($this->getIdentifier(), 'validate');
    }

    /**
     * get url payment link
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->url($this->getIdentifier(), 'payment');
    }
}
