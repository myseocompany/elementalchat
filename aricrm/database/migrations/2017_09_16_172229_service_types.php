<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('service_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('logo_url');

            $table->timestamps();
        });

        DB::table('service_types')->insert(array('name'=>'Laboral', 'logo_url'=>'laboral.png'));
        DB::table('service_types')->insert(array('name'=>'Ambiental', 'logo_url'=>'ambiental.png'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('service_types');
    }
}
