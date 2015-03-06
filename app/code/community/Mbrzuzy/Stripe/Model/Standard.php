<?php

require_once(Mage::getBaseDir('lib') . DS . 'Stripe' . DS . 'Stripe.php');

class Mbrzuzy_Stripe_Model_Standard extends Mage_Payment_Model_Method_Cc
{
    protected $_code          = 'stripe';
    protected $_formBlockType = 'stripe/standard_form';
    protected $_infoBlockType = 'payment/info';

    protected $_isGateway     = true;
    protected $_canAuthorize  = true;
    protected $_canCapture    = true;
    protected $_canRefund     = true;

    public function __construct()
    {
        $this->_code = Mage::helper('stripe')->getMethodCode();
        $key         = Mage::helper('stripe')->getSecretKey();

        Stripe::setApiKey(Mage::helper('core')->decrypt($key));
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('stripe/standard_form', $name)
            ->setMethod('stripe_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('mbrzuzy/stripe/standard/form.phtml');

        return $block;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $stateObject->setStatus(self::STATUS_UNKNOWN);
        $stateObject->setIsNotified(false);
    }

    public function getConfigPaymentAction()
    {
        return $this->getConfigData('payment_action');
    }

    public function validate()
    {
        $params = Mage::app()->getRequest()->getParams();

        if (isset($params['payment']['stripe_token']) && !empty($params['payment']['stripe_token'])) {
            return $this;
        }

        return parent::validate();
    }

    public function capture(Varien_Object $payment, $amount)
    {
        return $this->_charge($payment, $amount, self::ACTION_AUTHORIZE_CAPTURE);
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_charge($payment, $amount, self::ACTION_AUTHORIZE);
    }

    public function refund(Varien_Object $payment, $amount)
    {
        return $this->_refund($payment, $amount);
    }

    protected function _charge($payment, $amount, $transactionType)
    {
        $data = array(
            'capture' => false,
            'amount' => $amount * 100
        );

        switch ($transactionType) {
            case self::ACTION_AUTHORIZE:
                $data['capture'] = false;
                $data = array_merge($data, $this->_preparePaymentData($payment));
                break;
            case self::ACTION_AUTHORIZE_CAPTURE:
                $data['capture'] = true;
                if (!$payment->getAdditionalInformation('charge_id')) {
                    $data = array_merge($data, $this->_preparePaymentData($payment));
                }
                break;
        }

        try {
            if ($chargeId = $payment->getAdditionalInformation('charge_id')) {
                $charge = Stripe_Charge::retrieve($chargeId);
                $charge->capture();
            } else {
                $charge = Stripe_Charge::create($data);
            }
        } catch (Exception $e) {
            $payment->setStatus(self::STATUS_ERROR);
            Mage::logException($e);
        }

        if ($charge->captured === false) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setCCTransId($charge->id)
                ->setTransactionId($charge->id)
                ->setIsTransactionClosed(0)
                ->setAdditionalInformation('charge_id', $charge->id);
        } else {
            $payment->setStatus(self::STATUS_SUCCESS)
                ->setTransactionId($charge->id)
                ->setIsTransactionClosed(1);
        }

        return $this;
    }

    protected function _preparePaymentData($payment)
    {
        $params = Mage::app()->getRequest()->getParams();

        if ($this->getConfigData('use_stripejs')) {
            $source = $params['payment']['stripe_token'];
        } else {
            $source = array(
                'object' => 'card',
                'number' => $params['payment']['cc_number'],
                'exp_month' => $params['payment']['cc_exp_month'],
                'exp_year' => $params['payment']['cc_exp_year'],
                'cvc' => $params['payment']['cc_cid']
            );
        }

        return array(
            'currency' => $payment->getOrder()->getBaseCurrencyCode(),
            'source' => $source,
            'description' => 'New charge for ' . $payment->getOrder()->getBillingAddress()->getName() . ' - Order #' . $payment->getOrder()->getId()
        );
    }

    protected function _refund($payment, $amount)
    {
        $transactionId = $this->_getParentTransactionId($payment);

        if ($transactionId) {
            try {
                $refund = Stripe_Charge::retrieve($transactionId)->refunds->create();
            } catch (Exception $e) {
                Mage::logException($e);
            }

            $payment->setRefundTransactionId($refund->id)
                ->setIsTransactionClosed(1)
                ->setShouldCloseParentTransaction(1);
        }

        return $this;
    }

    protected function _getParentTransactionId(Varien_Object $payment)
    {
        return $payment->getParentTransactionId() ? $payment->getParentTransactionId() : $payment->getLastTransId();
    }
}