<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_id');
            $table->string('name')->nullable();
            $table->string('service_type_id');
            $table->string('service_status_id');
            
            $table->bigInteger('project_budget')->nullable();
            $table->bigInteger('fee_budget')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();
        });

        DB::table('services')->insert(array('account_id'=>'1', 'service_type_id'=>'1', 'service_status_id'=>'1','project_budget'=>'10','name'=>'servicio1'));
        DB::table('services')->insert(array('account_id'=>'1', 'service_type_id'=>'2', 'service_status_id'=>'2','fee_budget'=>'20','name'=>'servicio2'));

        DB::table('services')->insert(array('account_id'=>'2', 'service_type_id'=>'1', 'service_status_id'=>'3', 'project_budget'=>'30','name'=>'servicio3'));
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
