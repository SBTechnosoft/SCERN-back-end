<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_mst')) {
            Schema::create('user_mst', function (Blueprint $table) {
                $table->increments('user_id');
                $table->char('user_name',35)->nullable()->collate('utf16_general_ci');
                $table->enum('user_type',array('admin', 'staff', 'superadmin', 'salesman'))->nullable()->collate('utf16_general_ci');
                $table->char('email_id',35)->nullable()->collate('utf16_general_ci');
                $table->char('password',35)->nullable()->collate('utf16_general_ci');
                $table->char('contact_no',15)->nullable()->collate('utf16_general_ci');
                $table->char('address',100)->nullable()->collate('utf16_general_ci');
                $table->char('pincode',10)->nullable()->collate('utf16_general_ci');
                $table->char('permission_array',5000)->nullable()->collate('utf16_general_ci');
                $table->integer('default_company_id');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->default('0000-00-00 00:00:00');
                $table->dateTime('deleted_at')->nullable()->default('0000-00-00 00:00:00');
                $table->char('state_abb',5)->collate('utf16_general_ci');
                $table->integer('city_id');
                $table->integer('company_id');
                $table->integer('branch_id');

                $table->index('company_id');
                $table->index('branch_id');
                $table->index('city_id');
                $table->index('email_id');
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
        Schema::drop('user_mst');
    }
}
