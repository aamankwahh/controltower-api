<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('request_type',['STATE_CHANGE','LOCATION_UPDATE','WEATHER_INFO']);
            $table->string('action')->nullable;
            $table->tinyInteger('status');
            $table->bigInteger('aircraft_id')->unsigned();
            $table->foreign('aircraft_id')->references('id')->on('aircraft');
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
        Schema::dropIfExists('request_logs');
    }
};
