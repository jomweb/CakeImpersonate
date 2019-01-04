<?php
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
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
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

        $this->Impersonate->Impersonate->setConfig('stayLoggedIn', false);
        $this->Impersonate->getRequest()->getSession()->write('OriginalAuth', $this->Auth);
        $this->assertSame('/', $this->Impersonate->Impersonate->logout());
    }

    /**
     * @return void
     * @expectedException \Cake\Controller\Exception\AuthSecurityException
     * @expectedExceptionMessage You must configure the Impersonate.sessionKey in config/app.php when impersonating a user.
     */
    public function testLogin()
    {
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('POST'));
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertNull($this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     * @expectedException \Cake\Controller\Exception\AuthSecurityException
     * @expectedExceptionMessage You can only call the login function with a request that is POST, PUT, or DELETE
     */
    public function testLoginGet()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('GET'));
        $this->Impersonate->getRequest()->getSession()->write('Auth', $this->Auth);
        $this->assertTrue($this->Impersonate->Impersonate->login(1));

        $this->assertNull($this->Impersonate->getRequest()->getSession()->read('OriginalAuth'));
    }

    /**
     * @return void
     */
    public function testLoginConfiged()
    {
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('PUT'));
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
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('POST'));
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
        Configure::write('Impersonate.sessionKey', 'OriginalAuth');
        $this->Impersonate->setRequest($this->Impersonate->getRequest()->withMethod('DELETE'));
        $this->Impersonate->Impersonate->setConfig('finder', 'Peanuts');
        $this->Impersonate->Impersonate->login(1);
    }
}
