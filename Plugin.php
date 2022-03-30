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
            'Beysong\Wechat\Components\ShareSetting' => 'ShareSetting',
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
        App::registerClassAlias('EasySocialite', \Overtrue\LaravelSocialite\Socialite::class);

        // Register the service providers provided by the packages used by your plugin
        App::register(\Overtrue\LaravelSocialite\ServiceProvider::class);
    }

    public function boot()
    {

        Config::set('socialite', Config::get('beysong.wechat::socialite'));
            
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
 
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'hex2hsl' => [$this, 'hexToHsl']
            ]
        ];
    }

    public function hexToHsl($hex) {
        $hex = array(substr($hex, 1, 2), substr($hex, 3, 2), substr($hex, 5, 2));
        $rgb = array_map(function($part) {
            return hexdec($part) / 255;
        }, $hex);

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch($max) {
                case $rgb[0]:
                    $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                    break;
                case $rgb[1]:
                    $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                    break;
                case $rgb[2]:
                    $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                    break;
            }

            $h /= 6;
        }
        
        return ($h*360).' '.($s*100).'% '.($l*100).'%';
        
    }

}
