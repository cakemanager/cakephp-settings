<?php

namespace Settings\Controller;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{

    public function isAuthorized($user) {

        $this->Authorizer->action(['*'], function($auth){
            $auth->allowRole(1);
        });

        return $this->Authorizer->authorize();
    }

}
