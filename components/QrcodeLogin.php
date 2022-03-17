<?php namespace Beysong\Wechat\Components;

use Session;
use Lang;
use Auth;
use Event;
use Flash;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Cms\Classes\Router;
use ValidationException;
use Beysong\Wechat\Models\Settings;
use October\Rain\Auth\Models\User;
use Beysong\Wechat\Models\WechatUser;

class QrcodeLogin extends ComponentBase
{
    public $appid;
    public $secret;


    public function componentDetails()
    {
        return [
            'name'        => 'Qrcode Login',
            'description' => 'Qrcode Login'
        ];
    }

    /**
    * Executed when this component is bound to a page or layout.
    */
    public function onRun()
    {
        $this->appid = Settings::get('open_appid', 'appid-test');
        $wechatuser = Session::get('beysong.wechat.user');
        $user = $this->user();
    
        $this->page['user'] = $user;
        if ($user) {
            $this->page['bindeduser'] = $user->beysong_wechat_user()->first();
        } else {
            $this->page['bindeduser'] = null;
        }
        $this->page['wechatuser'] = $wechatuser;
    }

    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function onUnbindUser()
    {
        if (!Auth::check()) {
            return response()->json('请先登录', 401);
        }
        $user = Auth::user();
        if ($user->beysong_wechat_user) {
            $user->beysong_wechat_user()->delete();
            $user->save();
            // return response()->json('success', 200);
            return Redirect::to(Request::path()); // refresh page
        } else {
            return response()->json('error', 440);
        }
    }
}
