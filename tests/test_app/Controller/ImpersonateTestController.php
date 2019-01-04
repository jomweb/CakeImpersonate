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
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Use Controller instead of AppController to avoid conflicts
 *
 * @property \CakeImpersonate\Controller\Component\ImpersonateComponent $Impersonate
 * @property \App\Model\Table\UsersTable $Users
 */
class ImpersonateTestController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->loadComponent('Auth');
        $this->loadModel('Users');
        $this->loadComponent('CakeImpersonate.Impersonate');
        parent::initialize();
    }
}
