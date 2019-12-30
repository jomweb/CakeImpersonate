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
namespace CakeImpersonate\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Exception\AuthSecurityException;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * Impersonate component
 */
class ImpersonateComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'userModel' => 'Users',
        'finder' => 'all',
        'stayLoggedIn' => true,
    ];

    /**
     * Function impersonate
     *
     * @param mixed $id ID of user to impersonate
     * @return bool
     * @throws \Exception If userModal is not loaded in the Controller
     */
    public function login($id)
    {
        if (!is_string($this->getSessionKey())) {
            throw new AuthSecurityException('You must configure the Impersonate.sessionKey in config/app.php when impersonating a user.');
        }
        if (!$this->isPosted()) {
            throw new AuthSecurityException('You can only call the login function with a request that is POST, PUT, or DELETE');
        }
        $userModel = $this->getConfig('userModal', 'Users');
        $this->getController()->loadModel($userModel);

        $finder = $this->getConfig('finder', 'all');
        /** @var \Cake\ORM\Table $usersTable */
        $usersTable = $this->getController()->{$userModel};
        $userArray = $usersTable->find($finder)->where([$usersTable->getAlias() . '.id' => $id])->firstOrFail()->toArray();
        $originalAuth = $this->getController()->getRequest()->getSession()->read('Auth');
        $this->getController()->Auth->setUser($userArray);
        $this->getController()->getRequest()->getSession()->write($this->getSessionKey(), $originalAuth);

        return true;
    }

    /**
     * Function logout
     *
     * To log out of impersonated account
     *
     * @return bool|string Normalized config `logoutRedirect`
     */
    public function logout()
    {
        if (!is_string($this->getSessionKey())) {
            return true;
        }
        if ($this->isImpersonated()) {
            $Auth = $this->getController()->getRequest()->getSession()->read($this->getSessionKey());
            $this->getController()->getRequest()->getSession()->write('Auth', $Auth);
            $this->getController()->getRequest()->getSession()->delete($this->getSessionKey());
            $stayLoggedIn = $this->getConfig('stayLoggedIn', true);
            if (!$stayLoggedIn) {
                return $this->getController()->Auth->logout();
            }
        }

        return true;
    }

    /**
     * Function isImpersonated
     *
     * To check if current Auth is being impersonated
     *
     * @return bool
     */
    public function isImpersonated()
    {
        if (!is_string($this->getSessionKey())) {
            return false;
        }

        return $this->getController()->getRequest()->getSession()->check($this->getSessionKey());
    }

    /**
     * {@inheritdoc}
     */
    public function implementedEvents(): array
    {
        $eventMap = [
            'Controller.startup' => 'updateConfig',
        ];
        $events = [];
        foreach ($eventMap as $event => $method) {
            if (method_exists($this, $method)) {
                $events[$event] = $method;
            }
        }

        return $events;
    }

    /**
     * Updates the userModel and finder based on the AuthComponent.
     *
     * @param Event $event Event that started the update.
     * @return void
     */
    public function updateConfig(Event $event)
    {
        $this->setConfig('userModel', $this->getController()->Auth->getConfig('authorize.all.userModel', $this->getConfig('userModel')));
        $this->setConfig('finder', $this->getController()->Auth->getConfig('authorize.all.finder', $this->getConfig('finder')));
    }

    /**
     * Gets the current session key to look for our stored data
     *
     * @return null|string Session key to use
     */
    protected function getSessionKey()
    {
        return Configure::read('Impersonate.sessionKey');
    }

    /**
     * Checks if the request is POST, PUT or DELETE to give CsfrComponent and SecurityComponent a chance to inspect the request.
     *
     * @return bool If the request is POST, PUT, or DELETE
     */
    protected function isPosted()
    {
        return $this->getController()->getRequest()->is(['post', 'put', 'delete']);
    }
}
