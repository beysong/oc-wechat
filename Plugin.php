<?php namespace Beysong\Wechat;

use App;
use Event;
use Config;
use Session;
use RainLab\User\Models\User;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use Beysong\Wechat\Models\WechatUser;

class Plugin extends PluginBase
{
    public $require = ['Rainlab.User'];
    
    public function pluginDetails()
    {
        return [
            'name'        => 'beysong.wechat::lang.plugin.name',
            'description' => 'beysong.wechat::lang.plugin.description',
            'author'      => 'Beysong',
            'icon'        => 'icon-user',
            'homepage'    => 'https://github.com/beysong/oc-wechat'
        ];
    }

    public function registerComponents()
    {
        return [
            // 'Beysong\Wechat\Components\Binding' => 'Binding',
            // 'Beysong\Wechat\Components\QrcodeLogin' => 'QrcodeLogin',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Wechat Login',
                'description' => 'Manage Social Login providers.',
                'icon'        => 'icon-users',
                'class'       => 'Beysong\Wechat\Models\Settings',
                'order'       => 600,
                'permissions' => ['rainlab.users.access_settings'],
            ]
        ];
    }
    
    public function register()
    {
        // Register the aliases provided by the packages used by your plugin
        App::registerClassAlias('EasyWeChat', \Overtrue\LaravelWeChat\Facade::class);
        App::registerClassAlias('EasySocialite', \Overtrue\LaravelSocialite\Socialite::class);

        // Register the service providers provided by the packages used by your plugin
        App::register(\Overtrue\LaravelWeChat\ServiceProvider::class);
        App::register(\Overtrue\LaravelSocialite\ServiceProvider::class);
    }

    public function boot()
    {
        Config::set('wechat', Config::get('beysong.wechat::wechat'));
        Config::set('socialite', Config::get('beysong.wechat::socialite'));

        // dd(\Config::get('wechat'));
        // $aliasLoader = AliasLoader::getInstance();
        // $aliasLoader->alias('EasyWeChat', '\Overtrue\LaravelWeChat\Facade');
        // $aliasLoader->alias('EasySocialite', '\Overtrue\LaravelSocialite\Socialite');
        
        // App::register('\Overtrue\LaravelWeChat\ServiceProvider');
        // App::register('\Overtrue\LaravelSocialite\ServiceProvider');


        // Setup required packages
        User::extend(function ($model) {
            $model->hasOne['beysong_wechat_user'] = ['Beysong\Wechat\Models\WechatUser'];
        });

        Event::listen('rainlab.user.login', function ($user) {
            $wid = Session::get('beysong.wechat.userid');
            if ($wid) {
                $authUser = WechatUser::where('oauth_id', $wid)->first();
                $authUser->user_id = $user->id;
                $authUser->save();
            }
        });
    }
 
}
