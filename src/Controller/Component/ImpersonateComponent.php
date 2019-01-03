<?php
/**
 * Impersonate component for Cakephp3
 *
 * Component to use impersonate to easily access as others user account
 *
 *
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

        $finder = $this->getConfig('finder');
        /** @var \Cake\ORM\Table $userTable */
        $userTable = $this->getController()->{$userModel};
        $userArray = $userTable->find($finder)->where([$userTable->getAlias() . '.id' => $id])->firstOrFail()->toArray();
        $this->getController()->Auth->setUser($userArray);
        $this->getController()->getRequest()->getSession()->write('OriginalAuth', $this->getController()->getRequest()->getSession()->read('Auth'));

        return true;
    }

    /**
     * Function isImpersonate
     *
     * To check if current account is being impersonated
     * @return bool
     */
    public function isImpersonate()
    {
        if ($this->getController()->getRequest()->getSession()->read('OriginalAuth')) {
            return true;
        }

        return false;
    }

    /**
     * Function logout
     *
     * To log out of impersonated account
     *
     * @return bool
     */
    public function logout()
    {
        if ($this->isImpersonate()) {
            $Auth = $this->getController()->getRequest()->getSession()->read('OriginalAuth');
            $this->getController()->getRequest()->getSession()->write('Auth', $Auth);
            $this->getController()->getRequest()->getSession()->delete('OriginalAuth');
        }

        return true;
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
