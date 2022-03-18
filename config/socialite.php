<?php

use Beysong\Wechat\Models\Settings;

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