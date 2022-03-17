<?php namespace Beysong\Wechat\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class OauthUser extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Beysong.Wechat', 'main-menu-item', 'side-menu-item3');
    }
}
