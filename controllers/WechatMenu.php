<?php namespace Beysong\Wechat\Controllers;


use EasyWeChat;
use Backend\Classes\Controller;
use BackendMenu;
use Beysong\Wechat\Models\WechatMenu as WechatMenuModel;

class WechatMenu extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Beysong.Wechat', 'main-menu-item', 'side-menu-item2');
    }

    public function index()
    {
        parent::index();
        $app = EasyWeChat::officialAccount();
       
        $list = $app->menu->list();
         
        $this->vars['menuList'] = $list;
        // return '';
        // dd($list);
        // $buttons = [
        //     [
        //         "type" => "click",
        //         "name" => "今日歌曲",
        //         "key"  => "V1001_TODAY_MUSIC"
        //     ],
        //     [
        //         "name"       => "菜单",
        //         "sub_button" => [
        //             [
        //                 "type" => "view",
        //                 "name" => "搜索",
        //                 "url"  => "http://www.soso.com/"
        //             ],
        //             [
        //                 "type" => "view",
        //                 "name" => "视频",
        //                 "url"  => "http://v.qq.com/"
        //             ],
        //             [
        //                 "type" => "click",
        //                 "name" => "赞一下我们",
        //                 "key" => "V1001_GOOD"
        //             ],
        //         ],
        //     ],
        // ];
        // $app->menu->create($buttons);

        // $list = $app->menu->list();
        // dd($list);
    }

    public function onSaveToWechat()
    {
        $app = EasyWeChat::officialAccount();
        
        $list = $app->menu->list();
        
        // $buttons = [
        //     [
        //         "type" => "click",
        //         "name" => "今日歌曲",
        //         "key"  => "V1001_TODAY_MUSIC"
        //     ],
        //     [
        //         "name"       => "菜单",
        //         "sub_button" => [
        //             [
        //                 "type" => "view",
        //                 "name" => "搜索",
        //                 "url"  => "http://www.soso.com/"
        //             ],
        //             [
        //                 "type" => "view",
        //                 "name" => "视频",
        //                 "url"  => "http://v.qq.com/"
        //             ],
        //             [
        //                 "type" => "click",
        //                 "name" => "赞一下我们",
        //                 "key" => "V1001_GOOD"
        //             ],
        //         ],
        //     ],
        // ];
        $parents = WechatMenuModel::where('parent_id', -1)->orWhere('parent_id', 0)->get();
        $children = WechatMenuModel::where('parent_id', '<>', null)->where('parent_id', '<>', 0)->get();
        $buttons = [];
        foreach ($parents as $v) {
            $buttons[] = [
                "type" => $v["type"],
                "name" => $v["name"],
                "key" => $v["key"],
                "id" => $v["id"],
                "appid" => $v["appid"],
                "url" => $v["url"],
                "pagepath" => $v["pagepath"],
                "media_id" => $v["media_id"],
            ];
        }
        foreach ($buttons as $k=>$v) {
            foreach ($children as $vv) {
                if ($v['id'] == $vv['parent_id']) {
                    $buttons[$k]['sub_button'][]=[
                        "type" => $vv["type"],
                        "name" => $vv["name"],
                        "key" => $vv["key"],
                        "appid" => $vv["appid"],
                        "url" => $vv["url"],
                        "pagepath" => $vv["pagepath"],
                        "media_id" => $vv["media_id"],
                    ];
                }
            }
        }
        $result = $app->menu->create($buttons);
        return $result;
    }
}
