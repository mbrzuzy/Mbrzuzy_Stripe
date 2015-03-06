<?php
class Mbrzuzy_Stripe_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_helper = new Mbrzuzy_Stripe_Helper_Data;
    }

    public function testGetMethodCode()
    {
        $this->assertEquals('stripe', $this->_helper->getMethodCode());
    }

    public function testIsUsingStripeJs()
    {
        $this->assertInternalType('boolean', $this->_helper->isUsingStripeJs());
    }

    public function testGetPublishableKey()
    {
        $this->assertInternalType('string', $this->_helper->getPublishableKey());
    }

    public function testGetSecretKey()
    {
        $this->assertInternalType('string', $this->_helper->getSecretKey());
    }
}