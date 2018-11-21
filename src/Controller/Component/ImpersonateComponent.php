<?php
/**
 * Impersonate component for Cakephp3
 * 
 * Component to use impersonate to easily access as others user account
 * 
 * 
 */
namespace CakeImpersonate\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Table;
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
     * Initialize and use all Controller library
     *
     * return none
     */
    public function initialize(array $config) 
    {
        $this->controller = $this->_registry->getController();
       
    }
     
    /**
     * Function impersonate
     *
     * receive user Id
     * return true
     */
    public function login($id){
        
        $this->controller->loadModel('Users');
        
        $originalAuth = $this->request->getSession()->read('Auth');
        
        $users = $this->controller->Users->get($id);
        $this->controller->Auth->setUser($users);
        $this->request->getSession()->write('OriginalAuth',$originalAuth);
       
        return true;
    }
    
    /**
     * Function isImpersonate
     *
     * To check wether current account is under impersonate
     * return boolean
     */
    public function isImpersonate() {
        
        if($this->request->getSession()->read('OriginalAuth')){
            return true;
        }
        
        return false;
    }
    
    /**
     * Function logout
     *
     * To logout impersonate account
     * return true
     */
    public function logout() {
        
        if($this->isImpersonate()) {
            $Auth = $this->request->session()->read('OriginalAuth');
             $this->request->getSession()->write('Auth',$Auth);
            
            $this->request->getSession()->delete('OriginalAuth');
        }
        
        return true;
    }
}
