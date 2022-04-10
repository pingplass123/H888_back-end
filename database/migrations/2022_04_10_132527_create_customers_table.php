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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('idCustomer')->unsigned();
            $table->bigInteger('idUser')->unsigned();
            $table->string('name');
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });

        Schema::table('customers', function($table) {
            $table->foreign('idUser')->references('idUser')->on('users');
            $table->foreign('created_by')->references('idAdmin')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
