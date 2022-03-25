<?php namespace Beysong\Wechat\Components;

use Session;
use Lang;
use Auth;
use Request;
use Config; 
use Cms\Classes\ComponentBase; 
use Beysong\Wechat\Models\Settings; 
use EasyWeChat\Factory as EasyWeChat;

class ShareSetting extends ComponentBase
{
    public $appid;
    public $secret;


    public function componentDetails()
    {
        return [
            'name'        => 'Share Setting',
            'description' => 'Wechat share setting'
        ];
    }


    public function defineProperties()
    {
        return [
            'title' => [
                'title'       => 'Title',
                'description' => 'Share Title',
                'type'        => 'string',
                'default'     => ''
            ],
            'desc' => [
                'title'       => 'Description',
                'description' => 'Share Description on in share timeline',
                'type'        => 'string',
                'default'     => ''
            ],
            'link' => [
                'title'       => 'Link',
                'description' => 'Share Link, default is location.href',
                'type'        => 'string',
                'default'     => '',
                'validationPattern' => 'https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)',
                'validationMessage' => 'Invalid Link'
            ],
            'imgUrl' => [
                'title'       => 'Share Icon',
                'description' => 'Share Icon absolute url, only allow jpg or png',
                'type'        => 'string',
                'default'     => '',
                'required'    => true,
                'validationPattern' => 'https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)',
                'validationMessage' => 'Invalid Icon Uri'
            ],
        ];
    }

    /**
    * Executed when this component is bound to a page or layout.
    */
    public function onRun()
    {
        $this->addJs('//res.wx.qq.com/open/js/jweixin-1.6.0.js');
        $this->addJs('/plugins/beysong/wechat/assets/wechat.js');
    }

    public function onGetShareData()
    {
        return $this->getProperties();
    }
}
