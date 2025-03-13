<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AccountFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
       public function up()
    {
        //
        //
        Schema::create('account_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('url');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('account_files');
    }

}
