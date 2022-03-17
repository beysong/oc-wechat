<?php namespace Beysong\Wechat\Models;

use Model;

/**
 * Model
 */
class WechatMenu extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'beysong_wechat_menu';
    public function getParentIdOptions(){
        $p_options = self::where('parent_id', NULL)->orWhere('parent_id', -1)->get();
        // dd($p_options);
        // return $p_options?$p_options->toArray():[];
        $reset_options = ['-1'=>'Main Menu'];
        foreach ($p_options as $item) {
            $reset_options[$item->id] = $item->name;
        }
        return $reset_options;
    }
}
