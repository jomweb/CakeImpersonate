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
use Cake\ORM\Entity;

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
    protected $_defaultConfig = [];

    /**
     * Function impersonate
     *
     * @param mixed $id
     * @return bool
     */
    public function login($id)
    {
        $this->getController()->loadModel('Users');

        $originalAuth = $this->getController()->getRequest()->getSession()->read('Auth');

        /** @var Entity $users */
        $users = $this->getController()->Users->get($id);
        $this->getController()->Auth->setUser($users->toArray());
        $this->getController()->getRequest()->getSession()->write('OriginalAuth', $originalAuth);

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
}
