<?php
namespace CakeImpersonate\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Impersonate\Controller\Component\ImpersonateComponent;

/**
 * CakeImpersonate\Controller\Component\ImpersonateComponent Test Case
 */
class ImpersonateComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \CakeImpersonate\Controller\Component\ImpersonateComponent
     */
    public $Impersonate;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Impersonate = new ImpersonateComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Impersonate);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
