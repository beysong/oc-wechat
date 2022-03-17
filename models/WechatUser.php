<?php namespace Beysong\Wechat\Models;

use App;
use Str;
use Model;
use October\Rain\Support\Markdown;

/**
 * Post Model
 */
class WechatUser extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'beysong_wechat_user';

    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'oauth_id'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User']
    ];
}
