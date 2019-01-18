<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStateMstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('state_mst')) {
            Schema::create('state_mst', function (Blueprint $table) {
                $table->char('state_abb',5)->collate('utf16_general_ci');
                $table->char('state_name',35)->nullable()->collate('utf16_general_ci');
                $table->enum('is_display',['yes', 'no'])->default('yes')->collate('utf16_general_ci');
                $table->integer('state_code');

                $table->index('state_code');
                $table->primary('state_abb');
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
        Schema::drop('state_mst');
    }
}
