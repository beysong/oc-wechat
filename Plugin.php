<?php namespace Beysong\Wechat;

use Auth;
use App;
use Event;
use Config;
use Session;
use RainLab\User\Models\User;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use Beysong\Wechat\Models\Settings;
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
    

    public function boot()
    {
        // Setup required packages
        $this->bootPackages(); 
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

    public function bootPackages()
    {
        // Get the namespace of the current plugin to use in accessing the Config of the plugin
        $pluginNamespace = str_replace('\\', '.', strtolower(__NAMESPACE__));

        // Instantiate the AliasLoader for any aliases that will be loaded
        $aliasLoader = AliasLoader::getInstance();

        // Get the packages to boot
        $packages = Config::get($pluginNamespace . '::packages');
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 
            $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        
        // Boot each package
        foreach ($packages as $name => $options) {
            // Setup the configuration for the package, pulling from this plugin's config
            if (!empty($options['config']) && !empty($options['config_namespace'])) {
                if ($options['config_namespace']=='wechat') {
                    $options['config']['official_account'] =[
                        'default' => [
                            'app_id' => Settings::get('appid', 5011),         // AppID
                            'secret' => Settings::get('secret', 5012),   // AppSecret
                            'token' => Settings::get('token', 5013),           // Token
                            'aes_key' => Settings::get('aes_key', 501),               // EncodingAESKey

                            'oauth' => [
                                'scopes'   => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
                                'callback' => '/beysong/wechat/callback?auth_type=wechat',
                                // 'callback' => \Request::path(),
                            ],
                        ],
                    ];
                    $options['config']['open_platform'] =[
                        'default' => [
                            'app_id' => Settings::get('open_appid', 5011),         // AppID
                            'secret' => Settings::get('open_secret', 5012),   // AppSecret
                            'token' => Settings::get('open_token', 5013),           // Token
                            'aes_key' => Settings::get('aes_key', 501),               // EncodingAESKey

                            'oauth' => [
                                'scopes'   => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
                                'callback' => '/beysong/wechat/callback?auth_type=wechat',
                                // 'callback' => \Request::path(),
                            ],
                        ],
                    ];
                } elseif ($options['config_namespace']=='socialite') {
                    $options['config']['wechat'] = [
                        'client_id' => Settings::get('open_appid', 5011),
                        'client_secret' => Settings::get('open_secret', 5011),
                        'redirect' => $protocol.$domainName.'/beysong/wechat/open_callback?auth_type=wechat',
                        'scope' => 'snsapi_base',
                    ];
                }
                Config::set($options['config_namespace'], $options['config']);
            }

            // Register any Service Providers for the package
            if (!empty($options['providers'])) {
                foreach ($options['providers'] as $provider) {
                    App::register($provider);
                }
            }

            // Register any Aliases for the package
            if (!empty($options['aliases'])) {
                foreach ($options['aliases'] as $alias => $path) {
                    $aliasLoader->alias($alias, $path);
                }
            }
        }
    }
}
