<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemplateMstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('template_mst')) {
            Schema::create('template_mst', function (Blueprint $table) {
                $table->increments('template_id');
                $table->char('template_name',500)->nullable()->collate('utf16_general_ci');
                $table->longText('template_body')->nullable()->collate('utf16_general_ci');
                $table->char('template_type',400)->nullable()->collate('utf16_general_ci');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->default('0000-00-00 00:00:00');
                $table->integer('company_id');

                $table->index('company_id');
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
        Schema::drop('template_mst');
    }
}
