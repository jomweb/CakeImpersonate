<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\ImpersonateTestController;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\ImpersonateComponent Test Case
 */
class ImpersonateComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\ImpersonateTestController
     */
    public $Impersonate;

    public $fixtures = [
        'plugin.CakeImpersonate.Users'
    ];

    public $Auth = [
        'User' => [
            'id' => 1,
            'name' => 'test-user',
            'password' => '12345678',
            'active' => true
        ]
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Configure::write('App.fullBaseUrl', 'http://localhost');

        $request = new ServerRequest('/my_controller/foo');
        $request = $request->withParam('controller', 'MyController')
            ->withParam('action', 'foo');
        $this->Impersonate = new ImpersonateTestController($request);
        $this->Impersonate->startupProcess();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Impersonate);
        unset($this->Auth);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testIsImpersonate()
    {
        $this->assertFalse($this->Impersonate->Impersonate->isImpersonate());

        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->isImpersonate());
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $this->assertTrue($this->Impersonate->Impersonate->logout());

        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->logout());
        $this->assertEquals($this->Auth, $this->Impersonate->getRequest()->getSession()->read('Auth'));
    }

    /**
     * @return void
     */
    public function testLogin()
    {
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertEquals($this->Auth, $this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     * @expectedException \Exception
     */
    public function testLoginException()
    {
        $this->Impersonate->Impersonate->setConfig('userModal', 'UserNotFound');
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(2));

        $this->assertEquals($this->Auth, $this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }
}
