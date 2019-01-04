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
        'sessionKey' => 'OriginalAuth',
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
        $userModel = $this->getConfig('userModal', 'Users');
        $this->getController()->loadModel($userModel);

        $finder = $this->getConfig('finder', 'all');
        /** @var \Cake\ORM\Table $usersTable */
        $usersTable = $this->getController()->{$userModel};
        $userArray = $usersTable->find($finder)->where([$usersTable->getAlias() . '.id' => $id])->firstOrFail()->toArray();
        $this->getController()->Auth->setUser($userArray);
        $this->getController()->getRequest()->getSession()->write($this->getConfig('sessionKey', 'OriginalAuth'), $this->getController()->getRequest()->getSession()->read('Auth'));

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
        if ($this->isImpersonated()) {
            $Auth = $this->getController()->getRequest()->getSession()->read($this->getConfig('sessionKey', 'OriginalAuth'));
            $this->getController()->getRequest()->getSession()->write('Auth', $Auth);
            $this->getController()->getRequest()->getSession()->delete($this->getConfig('sessionKey', 'OriginalAuth'));
            $stayLoggedIn = $this->getConfig('stayLoggedIn', true);
            if (!$stayLoggedIn) {
                return $this->getController()->Auth->logout();
            }
        }

        return true;
    }

    /**
     * Function isImpersonate
     *
     * To check if current account is being impersonated
     * @deprecated 2.1.5 Will be removed in 3.0.0 use `isImpersonated()` instead
     * @return bool
     */
    public function isImpersonate()
    {
        deprecationWarning('isImpersonate() is deprecated use isImpersonated() instead');

        return $this->isImpersonated();
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
        return $this->getController()->getRequest()->getSession()->check('OriginalAuth');
    }

    /**
     * {@inheritdoc}
     */
    public function implementedEvents()
    {
        $eventMap = [
            'Controller.initialize' => 'updateConfig',
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
}
