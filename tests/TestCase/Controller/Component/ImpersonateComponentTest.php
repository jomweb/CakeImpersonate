<?php
/**
 * CakeImpersonate : Impersonate Plugin
 * Copyright (c) jomweb
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) jomweb
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller\Component;

use App\Controller\ImpersonateTestController;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\ImpersonateComponent Test Case
 */
class ImpersonateComponentTest extends TestCase
{
    use IntegrationTestTrait;

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
        $this->disableErrorHandlerMiddleware();
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
    public function testIsImpersonated()
    {
        $this->assertFalse($this->Impersonate->Impersonate->isImpersonated());

        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->isImpersonated());
    }

    /**
     * @return void
     */
    public function testIsImpersonate()
    {
        $this->assertFalse($this->Impersonate->Impersonate->isImpersonate());
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->logout());
        $this->assertSame($this->Auth, $this->Impersonate->getRequest()->getSession()->read('Auth'));
    }

    /**
     * @return void
     */
    public function testLogoutFully()
    {
        $this->Impersonate->Impersonate->setConfig('stayLoggedIn', false);
        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertSame('/', $this->Impersonate->Impersonate->logout());
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
     * @expectedException \Cake\Database\Exception
     */
    public function testUnloadableUserModal()
    {
        $this->Impersonate->Impersonate->setConfig('userModal', 'UserNotFound');
        $this->Impersonate->Impersonate->login(1);
    }

    /**
     * @return void
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Unknown finder method "Peanuts"
     */
    public function testUnloadableFinder()
    {
        $this->Impersonate->Impersonate->setConfig('finder', 'Peanuts');
        $this->Impersonate->Impersonate->login(1);
    }
}
