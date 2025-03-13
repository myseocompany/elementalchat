<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AccountStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('account_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        DB::table('account_statuses')->insert(array('name'=>'Prospecto'));
        DB::table('account_statuses')->insert(array('name'=>'Contactado'));
        DB::table('account_statuses')->insert(array('name'=>'Con propuesta'));
        
        DB::table('account_statuses')->insert(array('name'=>'Cliente'));
        DB::table('account_statuses')->insert(array('name'=>'Perdido'));
        DB::table('account_statuses')->insert(array('name'=>'Inactiva'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('account_statuses');
    }
}
