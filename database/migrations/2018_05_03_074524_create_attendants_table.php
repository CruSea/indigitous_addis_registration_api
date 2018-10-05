<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->integer('age')->nullable();
            $table->string('sex')->nullable();  //M for male or F for female
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('profession')->nullable();
            $table->string('academic_status')->nullable();
            $table->string('conference_year')->nullable();
            $table->string('conference_place')->nullable();
            $table->integer('is_confirmed')->nullable()->default(0);
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
        Schema::dropIfExists('attendants');
    }
}
