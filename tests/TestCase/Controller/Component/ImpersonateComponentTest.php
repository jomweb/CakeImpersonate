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
        'plugin.CakeImpersonate.Users',
    ];

    public $Auth = [
        'User' => [
            'id' => 1,
            'name' => 'test-user',
            'password' => '12345678',
            'active' => true,
        ],
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->disableErrorHandlerMiddleware();
        Configure::write('App.fullBaseUrl', 'http://localhost');

        $request = new ServerRequest();
        $request = $request->withParam('controller', 'MyController')
            ->withRequestTarget('/my_controller/foo')
            ->withParam('action', 'foo');
        $this->Impersonate = new ImpersonateTestController($request);
        $this->Impersonate->startupProcess();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
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
        $this->assertFalse($this->Impersonate->Impersonate->isImpersonated());
    }

    /**
     * @return void
     */
    public function testIsImpersonatedConfig()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->assertFalse($this->Impersonate->Impersonate->isImpersonated());

        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->isImpersonated());
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');

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
        $this->assertTrue($this->Impersonate->Impersonate->logout());
    }

    /**
     * @return void
     */
    public function testLogoutFullyConfigured()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        Configure::write('logoutRedirect', '/mycontroller/foo');

        $this->Impersonate->Impersonate->setConfig('stayLoggedIn', false);
        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertSame('/', $this->Impersonate->Impersonate->logout());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testLogin()
    {
        $this->expectException('\Cake\Controller\Exception\AuthSecurityException');
        $this->expectExceptionMessage('You must configure the Impersonate.sessionKey in config/app.php when impersonating a user.');

        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('POST'));
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertNull($this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testLoginGet()
    {
        $this->expectException('\Cake\Controller\Exception\AuthSecurityException');
        $this->expectExceptionMessage('You can only call the login function with a request that is POST, PUT, or DELETE');

        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('GET'));
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertNull($this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testLoginConfigured()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('PUT'));
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertEquals($this->Auth, $this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testUnloadableUserModal()
    {
        $this->expectException('\Cake\Database\Exception');

        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('POST'));
        $this->Impersonate->Impersonate->setConfig('userModal', 'UserNotFound');
        $this->Impersonate->Impersonate->login(1);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testUnloadableFinder()
    {
        $this->expectException('\BadMethodCallException');
        $this->expectExceptionMessage('Unknown finder method "Peanuts"');

        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('DELETE'));
        $this->Impersonate->Impersonate->setConfig('finder', 'Peanuts');
        $this->Impersonate->Impersonate->login(1);
    }
}
