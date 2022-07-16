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
        Schema::create('traffic_logs', function (Blueprint $table) {
            $table->id();
            //$table->unsignedInteger('aircraft_id');
            $table->bigInteger('aircraft_id')->unsigned();
            $table->boolean('accepted');
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
        Schema::dropIfExists('traffic_logs');
    }
};
