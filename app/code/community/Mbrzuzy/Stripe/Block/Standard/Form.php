<?php

class Mbrzuzy_Stripe_Block_Standard_Form extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mbrzuzy/stripe/standard/form.phtml');
    }
}