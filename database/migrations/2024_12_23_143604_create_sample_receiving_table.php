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
        Schema::create('sample_receiving', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('section');
            $table->string('client_name');
            $table->string('client_reference');
            $table->string('type_of_sample');
            $table->string('required_tests');
            $table->string('number_of_sample');
            $table->unsignedBigInteger('delivered_by');
            $table->unsignedBigInteger('received_by');
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('section')->references('id')->on('sample_categories');
            $table->foreign('delivered_by')->references('id')->on('employees');
            $table->foreign('received_by')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_receiving');
    }
};
