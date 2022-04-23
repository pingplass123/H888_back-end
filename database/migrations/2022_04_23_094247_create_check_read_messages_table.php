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
        Schema::create('check_read_messages', function (Blueprint $table) {
            $table->bigIncrements('idCheck')->unsigned();
            $table->bigInteger('idUser')->unsigned();
            $table->bigInteger('idRoom')->unsigned();
            $table->dateTime('latest_read');
            $table->timestamps();
        });

        Schema::table('check_read_messages', function ($table) {
            $table->foreign('idUser')->references('idUser')->on('users');
            $table->foreign('idRoom')->references('idRoom')->on('chat_rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_read_messages');
    }
};
