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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('idRoom')->unsigned();
            $table->bigInteger('idAdmin')->unsigned();
            $table->bigInteger('idCustomer')->unsigned();
            $table->timestamps();
        });

        Schema::table('chat_rooms', function($table) {
            $table->foreign('idAdmin')->references('idAdmin')->on('admins');
            $table->foreign('idCustomer')->references('idCustomer')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
