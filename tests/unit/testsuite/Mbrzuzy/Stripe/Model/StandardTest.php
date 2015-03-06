<?php
class Mbrzuzy_Stripe_Model_StandardTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Mcrypt is not loaded.');
        }

        $this->_model = new Mbrzuzy_Stripe_Model_Standard;
    }

    public function testIsGateway()
    {
        $this->assertEquals(true, $this->_model->isGateway());
    }

    public function testCanAuthorize()
    {
        $this->assertEquals(true, $this->_model->canAuthorize());
    }

    public function testCanCapture()
    {
        $this->assertEquals(true, $this->_model->canCapture());
    }

    public function testCanRefund()
    {
        $this->assertEquals(true, $this->_model->canRefund());
    }

    public function testGetCode()
    {
        $this->assertEquals('stripe', $this->_model->getCode());
    }

    public function testGetFormBlockType()
    {
        $this->assertEquals('stripe/standard_form', $this->_model->getFormBlockType());
    }

    public function testGetInfoBlockType()
    {
        $this->assertEquals('payment/info', $this->_model->getInfoBlockType());
    }
}