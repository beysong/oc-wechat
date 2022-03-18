<?php namespace Beysong\Wechat;

use Beysong\Wechat\Models\Settings;

$protocol = (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)?'https://':'http://';
$domainName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME']: '';

 return [
     //...
     'wechat' => [
        'client_id' => Settings::get('open_appid', 5011),
        'client_secret' => Settings::get('open_secret', 5011),
        'redirect' => $protocol.$domainName.'/beysong/wechat/open_callback?auth_type=wechat',
        'scope' => 'snsapi_base',
     ],
     //...
 ];