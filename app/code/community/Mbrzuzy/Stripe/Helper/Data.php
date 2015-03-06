<?php

class Mbrzuzy_Stripe_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_methodCode = 'stripe';

    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    public function isUsingStripeJs()
    {
        return (bool) Mage::getStoreConfig('payment/stripe/use_stripejs');
    }

    public function getPublishableKey()
    {
        if ($this->_isTestMode()) {
            return Mage::getStoreConfig('payment/stripe/test_publishable_key');
        }

        return Mage::getStoreConfig('payment/stripe/publishable_key');
    }

    public function getSecretKey()
    {
        if ($this->_isTestMode()) {
            return Mage::getStoreConfig('payment/stripe/test_secret_key');
        }

        return Mage::getStoreConfig('payment/stripe/secret_key');
    }

    protected function _isTestMode()
    {
        return (bool) Mage::getStoreConfig('payment/stripe/test');
    }
}