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

class Binding extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Bind Status',
            'description' => 'Show the user bind status'
        ];
    }

    /**
    * Executed when this component is bound to a page or layout.
    */
    public function onRun()
    {
        $wechatuser = Session::get('beysong.wechat.user');
        $user = $this->user();
        // $isAuthenticated = Auth::check();
        // if($isAuthenticated){

        // } else {

        // }
        $this->page['user'] = $user;
        if ($user) {
            $this->page['bindeduser'] = $user->beysong_wechat_user()->first();
        } else {
            $this->page['bindeduser'] = null;
        }
        $this->page['wechatuser'] = $wechatuser;
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    /**
     * bind user
     */
    public function onBindUser()
    {
        $wechatuser = Session::get('beysong.wechat.user');
        if (!Auth::check()) {
            return response()->json('请先登录', 401);
        }
        if (!$wechatuser) {
            return response()->json('微信未授权', 401);
        }

        $wechatUser = WechatUser::where('openid', $wechatuser->id)->first();
        $user = Auth::user();
        if (!$wechatUser) {
            $wechatUser = new WechatUser();
            $wechatUser['user_id'] = $user->id;
            $wechatUser['openid'] = $wechatuser->id;
            $originalUser = $wechatuser->getOriginal();
            if ($originalUser['unionid']) {
                $wechatUser['unionid'] = $originalUser['unionid'];
            }
        }
        $user->beysong_wechat_user = $wechatUser;
        $user->save();
        // return response()->json('success', 200);
        return Redirect::to(Request::path()); // refresh page
    }

    /**
     * 微信授权
     */
    public function onWechatAuthorize()
    {
        return Redirect::to('/beysong/wechat/auth?r='.Request::path());
    }

    /**
     * unbind user
     */
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
