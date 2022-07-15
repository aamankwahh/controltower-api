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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
           
            $table->boolean('can_land')->default(true);
            $table->boolean('can_takeoff')->default(true);
            $table->boolean('can_approach')->default(true);
            $table->integer('large_spots_occupied')->default(0);
            $table->integer('small_spots_occupied')->default(0);
            $table->integer('total_large_spots')->default(5);
            $table->integer('total_small_spots')->default(10);
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
        Schema::dropIfExists('trackers');
    }
};
