<?php namespace Beysong\Wechat\Classes;

use Auth;
use Event;
use Flash;
use Exception;
use Lang;
use Log;
use October\Rain\Auth\Models\User;
use Beysong\Wechat\Models\Wechatuser;
use RainLab\User\Models\Settings as UserSettings;

class UserManager
{
    use \October\Rain\Support\Traits\Singleton;

    /**
     * Finds and returns the user attached to the given provider. If one doesn't
     * exist, it's created. If one exists but isn't attached, it's attached.
     *
     * @param  array $provider_details   ['id'=>..., 'token'=>...]
     * @param  array $user_details       ['email'=>..., ...]
     *
     * @return User
     */
    public function find($user_details)
    {
        // Are we already attached?
        $wechatUser = Wechatuser::where('wechat_id', '=', $user_details['id'])->first();
        // dd($user_details);
        if ( !$wechatUser )
        {
            // Does a user with this email exist?
            if(isset($user_details['email'])){
                $user = Auth::findUserByLogin( $user_details['email'] );
            }else{
                $user = Auth::findUserByLogin( $user_details['id'].'@dev.com' );
            }
            // No user with this email exists - create one
            if ( !$user )
            {
                if (UserSettings::get('allow_registration')) {
                    // Register the user
                    $user = $this->registerUser($user_details);
                } else {
                    Flash::warning(Lang::get('rainlab.user::lang.account.registration_disabled'));
                    return $user;
                }
            }
            $wechatUser = new Wechatuser();
            $wechatUser['user_id'] = $user->id;
            $wechatUser['wechat_id'] = $user_details['id'];
            // dd($wechatUser);
            $wechatUser->user = $user;
            $wechatUser->save();
            return $user;
        }
        // Provider was found, return the attached user
        else
        {
            if(isset($user_details['email'])){
                $user = Auth::findUserByLogin( $user_details['email'] );
            }else{
                $user = Auth::findUserByLogin( $user_details['id'].'@dev.com' );
            }
        }

        return $user;
    }

    public function registerUser($user_details)
    {
        // Create a username if one doesn't exist
        // dd($user_details);
        if ( !isset($user_details['email']) )
            $user_details['email'] = $user_details['id'].'@dev.com';
        if ( !isset($user_details['username']) )
            $user_details['username'] = $user_details['email'];

        // Generate a random password for the new user
        $user_details['password'] = $user_details['password_confirmation'] = str_random(16);

        $user = Auth::register($user_details, true);

        return $user;
    }

    /**
     * 关联微信账户
     */
    public function attachWechat($user)
    {
        // Create a username if one doesn't exist
        // dd($user);
        if ( !isset($user['email']) )
            $user['email'] = $user['id'].'@dev.com';
        if ( !isset($user['username']) )
            $user['username'] = $user['email'];

        // Generate a random password for the new user
        $user['password'] = $user['password_confirmation'] = str_random(16);

        $user = Auth::register($user, true);

        return $user;
    }

}