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
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transfer_id');
            $table->unsignedBigInteger('from_account');
            $table->unsignedBigInteger('to_account');
            $table->double('transfer_amount', 15, 2);
            $table->foreign('from_account')->references('id')->on('accounts');
            $table->foreign('to_account')->references('id')->on('accounts');
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
        Schema::dropIfExists('cash_transfers');
    }
};
