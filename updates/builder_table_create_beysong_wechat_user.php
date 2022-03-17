<?php namespace Beysong\Wechat\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateBeysongWechatUser extends Migration
{
    public function up()
    {
        Schema::create('beysong_wechat_user', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->string('oauth_name');
            $table->string('oauth_id');
            $table->string('oauth_type');
            $table->string('oauth_access_token');
            $table->string('oauth_refresh_token');
            $table->integer('oauth_expires');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('beysong_wechat_user');
    }
}
