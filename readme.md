Relies on Rainlab.User

# Plugin Setting

**Settings > Misc > Weixin Login** set the appid and secret of Offical Account and Open Platform

# Authorize

## Authorize in Wechat App environment

Authorize url: [your domain]/beysong/wechat/auth

## Authorize with Wechat Open Platform

Authorize url: [your domain]/beysong/wechat/open

After authorize successfully, the user account will bind the Wechat User by openid If the user account has logined in.
Otherwhise the bind action will execute after the user accout login.
The user can login by Wechat Authorize next time.

# Usage

- install the plugin: `php artisan plugin:install Beysong.Wechat` or `composer require beysong/wechat-plugin`
- Add the below code to any where you want

```
<a href="/beysong/wechat/auth?r=/redirect_url">Offical Account Login</a> or
<a href="/beysong/wechat/open?r=/redirect_url">Open Platform Login</a>
```

the params 'r' for binded user redirect url.

# TODO

- Wechat Share
