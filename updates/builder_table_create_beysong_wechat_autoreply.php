<?php namespace Beysong\Wechat\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateBeysongWechatAutoreply extends Migration
{
    public function up()
    {
        Schema::create('beysong_wechat_autoreply', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->string('keywords');
            $table->string('reply');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('beysong_wechat_autoreply');
    }
}
