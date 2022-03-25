<?php

use \RainLab\User\Facades\Auth as Auth;
use Beysong\Wechat\Models\WechatUser;

use EasyWeChat\Factory as EasyWeChat;
use EasySocialite;

// ini_set('display_errors','On');
// error_reporting(-1);


Route::group([
    'prefix' => 'beysong/wechat',
], function () {


    Route::get('server', array('middleware' => ['web'], function () {
        $config = Config::get('beysong.wechat::wechat.official_account.default');
        $wechat = EasyWeChat::officialAccount($config);
        // 微信验证服务器
        // $response = $wechat->server->serve();
        // $response->send();exit;

        $wechat->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                if ($message['Event'] == 'subscribe') {
                    return '感谢关注';
                    break;
                }
                return '收到事件消息';
                break;
                case 'text':
                return '收到文字消息';
                break;
                case 'image':
                return '收到图片消息';
                break;
                case 'voice':
                return '收到语音消息';
                break;
                case 'video':
                return '收到视频消息';
                break;
                case 'location':
                return '收到坐标消息';
                break;
                case 'link':
                return '收到链接消息';
                break;
                // ... 其它消息
                default:
                return '收到其它消息';
                break;
            }
        });
        return $wechat->server->serve();
    }));
    Route::get('auth', array('middleware' => ['web'], function () {

        $config = Config::get('beysong.wechat::wechat.official_account.default');
        $redirectUrl = Input::get('r', '/');
        if ($redirectUrl) {
            Session::put('beysong.wechat.redirect_url', $redirectUrl);
        }

        $wechat = EasyWeChat::officialAccount($config);
        
        $oauth = $wechat->oauth;
        return $oauth->redirect();
    }));
    Route::get('callback', array('middleware' => ['web'], function () {
         
        $config = Config::get('beysong.wechat::wechat.official_account.default');
        $redirectUrl = Session::get('beysong.wechat.redirect_url');

        $wechat = EasyWeChat::officialAccount($config);
        $oauth = $wechat->oauth;
       
        $jscode = Input::get('code');
        $jsstate = Input::get('state');

        if ($jscode && $jsstate) {
           
            $tempuser = $oauth->user();
 
            Session::put('beysong.wechat.user', $tempuser);
 
            $authUser = WechatUser::where(['oauth_id' => $tempuser->id,'oauth_type' => 'wechat'])->first();
 
            if (empty($authUser)) {
                $authUser = new WechatUser();
                $authUser['oauth_id'] = $tempuser->id;
                $authUser['oauth_name'] = $tempuser->name ? $tempuser->name : $tempuser->nickname;
                $authUser['oauth_type'] = 'wechat';
                $authUser['oauth_access_token'] = $tempuser->access_token;
                $authUser['oauth_refresh_token'] = $tempuser->refresh_token;
                $authUser['oauth_expires'] = $tempuser->expires_in || 7200;
            }

            if (Auth::check()) {
                $authUser->user = Auth::getUser();
            }
            $authUser->save();

            if ($authUser->user) {
                Auth::login($authUser->user);
            } else {
                Session::put('beysong.wechat.userid', $tempuser->id);
                // return Redirect::to('/user/login');
            }
            return Redirect::to($redirectUrl);
        } else {
            return response()->json('error', 500);
        }
    }));

    Route::get('open', array('middleware' => ['web'], function () {
        $auth_type = Input::get('auth_type', 'wechat');
        $redirectUrl = Input::get('r', '/'); // redirect url after authorization

        if ($redirectUrl) {
            Session::put('beysong.wechat.redirect_url', $redirectUrl);
        }
        
        if ($auth_type) {
            return EasySocialite::driver($auth_type)->redirect();
        } else {
            return response()->json('auth type can not be empty', 500);
        }
    }));

    Route::get('open_callback', array('middleware' => ['web'], function () {
        $redirectUrl = Session::get('beysong.wechat.redirect_url');
        $auth_type = Input::get('auth_type');
        $from = Input::get('from');
         
        $tempuser = EasySocialite::driver($auth_type)->user();

        Session::put('beysong.wechat.user', $tempuser);

        // search by openid
        $authUser = WechatUser::where('oauth_id', $tempuser->id)->where('oauth_type', $auth_type)->first();

        if (empty($authUser)) {
            $authUser = new WechatUser();
            $authUser['oauth_id'] = $tempuser->id;
            $authUser['oauth_name'] = $tempuser->name ? $tempuser->name : $tempuser->nickname;
            $authUser['oauth_type'] = $auth_type;
            $authUser['oauth_access_token'] = $tempuser->access_token;
            $authUser['oauth_refresh_token'] = $tempuser->refresh_token;
            $authUser['oauth_expires'] = $tempuser->expires_in || 7200;
        }


        if (Auth::check()) {
            $authUser->user = Auth::getUser();
        }
        $authUser->save();

        if ($authUser->user) {
            Auth::login($authUser->user);
        } else {
            Session::put('beysong.wechat.userid', $tempuser->id);
            // return Redirect::to('/user/login');
        }
        return Redirect::to($redirectUrl);
    }));

    // wechat js config
    Route::get('jsconfig', array('middleware' => ['web'], function () {
        $url = Input::get('url');
        $config = Config::get('beysong.wechat::wechat.official_account.default');
        $app = EasyWeChat::officialAccount($config);
        $app->jssdk->setUrl($url);
        return $app->jssdk->buildConfig(array('updateAppMessageShareData', 'updateTimelineShareData'), false, false, false);
    }));


});
