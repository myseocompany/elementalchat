<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('service_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        DB::table('service_statuses')->insert(array('name'=>'Activo'));
        DB::table('service_statuses')->insert(array('name'=>'Suspendido'));
        DB::table('service_statuses')->insert(array('name'=>'Cancelado'));        
        DB::table('service_statuses')->insert(array('name'=>'Finalizado'));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('service_statuses');
    }
}
