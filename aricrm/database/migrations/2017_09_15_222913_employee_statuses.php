<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmployeeStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('employee_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->timestamps();
        });

        DB::table('employee_statuses')->insert(array('name'=>'Activo'));
        DB::table('employee_statuses')->insert(array('name'=>'Activo con novedad'));
        
        DB::table('employee_statuses')->insert(array('name'=>'Inactivo'));

        DB::table('employee_statuses')->insert(array('name'=>'Inactivo con novedad'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('employee_statuses');
    }
}
