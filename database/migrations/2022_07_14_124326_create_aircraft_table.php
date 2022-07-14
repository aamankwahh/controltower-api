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
        Schema::create('aircraft', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['PRIVATE,AIRLINER']);
            $table->string('callsign');
            $table->enum('state', ['PARKED', 'AIRBORNE','APPROACH','LANDED']);
            $table->string('actions');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('altitude')->nullable();
            $table->double('heading')->nullable();
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
        Schema::dropIfExists('aircraft');
    }
};
