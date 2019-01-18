<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStaffCommissionMstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('staff_commission_mst')) {
            Schema::create('staff_commission_mst', function (Blueprint $table) {
                $table->increments('commission_id');
                $table->integer('user_id');
                $table->enum('commission_status',['on', 'off'])->default('off')->collate('utf16_general_ci');
                $table->decimal('commission_rate',20,4)->default('0.0000');
                $table->enum('commission_rate_type',['flat', 'percentage'])->default('percentage')->collate('utf16_general_ci');
                $table->char('commission_type',255)->nullable()->collate('utf16_general_ci');
                $table->char('commission_calc_on',255)->nullable()->collate('utf16_general_ci');
                $table->text('commission_for')->nullable()->collate('utf16_general_ci');
                $table->timestamps();
                $table->timestamp('deleted_at')->default('0000-00-00 00:00:00');

                $table->index('user_id');
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
        Schema::drop('staff_commission_mst');
    }
}
