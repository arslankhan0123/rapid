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
        Schema::create('transfer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // Add this line for the foreign key
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')->on('branches');
            $table->unsignedBigInteger('to');
            $table->foreign('to')->references('id')->on('branches');
            $table->date('transfer_date');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('transfer');
    }
};
