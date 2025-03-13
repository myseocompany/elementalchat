<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Accounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('account_status_id')->nullable();
            $table->string('document')->nullable();
            
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            
            $table->integer('employee_number')->nullable();
            $table->bigInteger('project_budget')->nullable();
            $table->bigInteger('fee_budget')->nullable();
            
            $table->text('description')->nullable();
            $table->timestamps();
        });
        DB::table('accounts')->insert(array('name'=>'Super', 'fee_budget'=>'10000000', 'project_budget'=>'1000000', 'account_status_id'=>1));
        DB::table('accounts')->insert(array('name'=>'Normandy', 'fee_budget'=>'20000000', 'account_status_id'=>4));
        DB::table('accounts')->insert(array('name'=>'Coca-Cola', 'project_budget'=>'1000000', 'account_status_id'=>3));    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('accounts');
    }
}
