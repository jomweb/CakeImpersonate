<?php
/**
 * Created by PhpStorm.
 * User: challgren
 * Date: 2019-01-02
 * Time: 05:28
 */

namespace App\Controller;


use Cake\Controller\Controller;

/**
 * Use Controller instead of AppController to avoid conflicts
 *
 * @property \CakeImpersonate\Controller\Component\ImpersonateComponent $Impersonate
 */
class ImpersonateTestController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->loadComponent('Auth');
        $this->loadComponent('CakeImpersonate.Impersonate');
        parent::initialize();
    }

}
