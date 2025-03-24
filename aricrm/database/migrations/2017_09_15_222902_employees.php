<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Employees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('document')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->text('observation')->nullable();
            $table->string('position')->nullable();
            $table->date('birth_day')->nullable();
            $table->date('commencement_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->date('end_trial_date')->nullable();
            $table->date('notice_date')->nullable();
            $table->string('agreement_type')->nullable();
            $table->integer('employee_status_id')->nullable();

            $table->timestamps();
        });
        

        
        DB::table('employees')->insert(array('name'=>'Pepito Super', 'account_id'=>1,'document'=>'78','employee_status_id'=>1));
        DB::table('employees')->insert(array('name'=>'Juan Normandy', 'account_id'=>2, 'document'=>'10','employee_status_id'=>2));   
             
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('employees');
    }
}
