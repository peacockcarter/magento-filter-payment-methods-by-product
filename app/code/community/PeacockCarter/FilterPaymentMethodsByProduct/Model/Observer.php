<?php

/**
 * PeacockCarter
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    PeacockCarter
 * @package     FilterPaymentMethodsByProduct
 * @copyright  Copyright (c) 2016 PeacockCarter Ltd
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PeacockCarter_FilterPaymentMethodsByProduct_Model_Observer
{
    /**
     * @array
     */
    private $_restrictedMethods;

    /**
     * @var
     */
    private $_paymentMethodCode;

    /**
     * @var Mage_Sales_Model_Quote
     */
    private $quote;

    /**
     * @param Varien_Event_Observer $observer
     */
    public function productBasedPaymentMethod(Varien_Event_Observer $observer)
    {
        if (!$this->doesQuoteExist($observer)) {

            return;
        }
        $this->getPaymentMethodCode($observer);

        if ($this->isMethodEnabled()) {
            $this->setRestrictedMethodsForCart();
            $this->setMethodVisibility($observer);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    private function doesQuoteExist(Varien_Event_Observer $observer)
    {
        $this->quote = $observer->getEvent()->getQuote();

        return $this->quote && $this->quote->getId();
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    private function getPaymentMethodCode(Varien_Event_Observer $observer)
    {
        $MethodInstance           = $observer->getEvent()->getMethodInstance();
        $this->_paymentMethodCode = $MethodInstance->getCode();
    }

    /**
     * @return bool
     */
    private function isMethodEnabled()
    {
        $activePaymentMethods = $this->getActivePaymentMethodCodes();

        return in_array($this->_paymentMethodCode, $activePaymentMethods);
    }

    protected function setRestrictedMethodsForCart()
    {
        $products = $this->getProductsInCart();

        foreach ($products as $product) {
            $this->addRestrictedMethodsToArray($product->getProductId());
        }
    }

    /**
     * @return mixed
     */
    private function getProductsInCart()
    {
        return $this->quote->getAllVisibleItems();
    }

    /**
     * @param $productId
     *
     * @return string
     */
    private function addRestrictedMethodsToArray($productId)
    {
        $storeId = Mage::app()->getStore()->getStoreId();

        if ($this->isRestrictionEnabled($productId, $storeId)) {

            if ($this->isShippingCountryInNonRestrictedList($productId, $storeId)) {
                return;
            }

            if ($this->isBillingCountryInNonRestrictedList($productId, $storeId)) {
                return;
            }

            $restrictedMethodList = Mage::getResourceModel('catalog/product')
                                        ->getAttributeRawValue($productId, 'pc_restricted_payment_methods', $storeId);
            $this->_restrictedMethods .= $restrictedMethodList . ',';
        }
    }

    /**
     * @param $productId
     *
     * @return mixed
     */
    private function isRestrictionEnabled($productId, $storeId)
    {
        $filterPaymentMethods = Mage::getResourceModel('catalog/product')
                                    ->getAttributeRawValue($productId, 'pc_restrict_payment_methods', $storeId);

        return $filterPaymentMethods;
    }

    /**
     * @param $productId
     * @param $storeId
     *
     * @return bool
     */
    private function isShippingCountryInNonRestrictedList($productId, $storeId)
    {
        $nonRestrictedCountries = Mage::getResourceModel('catalog/product')
                                      ->getAttributeRawValue($productId, 'pc_non_restricted_countries', $storeId);

        $nonRestrictedCountries = explode(',', $nonRestrictedCountries);

        if (count($nonRestrictedCountries) > 0) {
            $ShippingAddress   = $this->quote->getShippingAddress();
            $shippingCountryId = $ShippingAddress->getCountryId();

            return in_array($shippingCountryId, $nonRestrictedCountries);
        }

        return false;
    }

    /**
     * @param $productId
     * @param $storeId
     *
     * @return bool
     */
    private function isBillingCountryInNonRestrictedList($productId, $storeId)
    {
        $nonRestrictedCountries = Mage::getResourceModel('catalog/product')
                                      ->getAttributeRawValue($productId, 'pc_non_restrict_bill_county', $storeId);

        $nonRestrictedCountries = explode(',', $nonRestrictedCountries);

        if (count($nonRestrictedCountries) > 0) {
            $BillingAddress   = $this->quote->getBillingAddress();
            $billingCountryId = $BillingAddress->getCountryId();

            return in_array($billingCountryId, $nonRestrictedCountries);
        }

        return false;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    private function setMethodVisibility(Varien_Event_Observer $observer)
    {
        $result = $observer->getEvent()->getResult();

        if ($result->isAvailable) {
            $result->isAvailable = $this->isMethodAllowed();
        }
    }

    /**
     * @return bool
     */
    private function isMethodAllowed()
    {
        $restricted_methods_array = explode(',', $this->_restrictedMethods);
        if (count($restricted_methods_array) > 0) {

            return !in_array($this->_paymentMethodCode, $restricted_methods_array);
        }

        return true;
    }

    /**
     * @return array
     */
    private function getActivePaymentMethodCodes()
    {
        $paymentMethods = array();

        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        if (isset($payments) && count($payments) > 0) {
            foreach ($payments as $paymentCode => $paymentModel) {
                $paymentMethods[] = $paymentCode;
            }
        }

        return $paymentMethods;
    }
}
