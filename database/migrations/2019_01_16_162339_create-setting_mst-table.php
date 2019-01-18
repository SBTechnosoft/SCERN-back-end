<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingMstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('setting_mst')) {
            Schema::create('setting_mst', function (Blueprint $table) {
                $table->increments('setting_id');
                $table->char('setting_type',20)->nullable()->collate('utf16_general_ci');
                $table->text('setting_data')->nullable()->collate('utf16_general_ci');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('setting_mst');
    }
}
