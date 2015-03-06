<?php
class Mbrzuzy_Stripe_Block_Standard_FormTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_block = new Mbrzuzy_Stripe_Block_Standard_Form;
    }

    public function testIsInitialized()
    {
        $this->assertInstanceOf('Mbrzuzy_Stripe_Block_Standard_Form', $this->_block);
    }

    public function testBlockTemplateSet()
    {
        $this->assertNotEmpty($this->_block->getTemplate());
    }
}