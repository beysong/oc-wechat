<?php namespace Beysong\Wechat;

use System\Classes\PluginBase;
use Config;
use App;

/**
 * The plugin.php file (called the plugin initialization script) defines the plugin information class.
 */
class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name' => 'Wechat',
            'description' => 'Wechat.',
            'author' => 'Beysong',
            'icon' => 'icon-leaf'
        ];
    }
    public function boot()
    {
        Config::set('easywechat', Config::get('beysong.tools::easywechat'));
    }

    public function register()
    {
        // Register the aliases provided by the packages used by your plugin
        App::registerClassAlias('EasySocialite', \Overtrue\LaravelSocialite\Socialite::class);
        // Register the service providers provided by the packages used by your plugin
        App::register(\Overtrue\LaravelSocialite\ServiceProvider::class);

        // Register the aliases provided by the packages used by your plugin
        App::registerClassAlias('EasyWeChat', \Overtrue\LaravelWeChat\EasyWeChat::class);
        // Register the service providers provided by the packages used by your plugin
        App::register(\Overtrue\LaravelWeChat\ServiceProvider::class);
    }

    public function registerComponents()
    {
        return [
        ];
    }
}
