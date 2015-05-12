<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{

    /**
     * initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadComponent('Flash');

        $this->loadComponent('CakeManager.Manager');
        $this->loadComponent('Utils.Authorizer');
        
        $this->Auth->allow(['display']);
    }

    /**
     * beforeFilter
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * isAuthorized
     *
     * @param array $user User.
     * @return bool
     */
    public function isAuthorized($user)
    {
        return false;
    }
}
