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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('idMessage')->unsigned();
            $table->bigInteger('idRoom')->unsigned();
            $table->bigInteger('sentFrom')->unsigned();
            $table->binary('image');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE 'chat_messages' ALTER COLUMN image TYPE MEDIUMBLOB");

        Schema::table('chat_messages', function($table) {
            $table->foreign('idRoom')->references('idRoom')->on('chat_rooms');
            $table->foreign('sentFrom')->references('idUser')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
