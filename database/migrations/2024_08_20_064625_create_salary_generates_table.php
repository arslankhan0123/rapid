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
        Schema::create('salary_generates', function (Blueprint $table) {
            $table->id();
            $table->string('salary_month', 7)->unique(); // To store the salary year and month as a string (e.g., '2024-08')
            $table->date('generate_date'); // To store the date when the salary was generated
            $table->unsignedBigInteger('generated_by'); // Add this line for the foreign key
            $table->foreign('generated_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable(); // Add this line for the foreign key
            $table->foreign('approved_by')->references('id')->on('users');
            $table->boolean('status')->default(0); // To store the status, with a default value of 1
            $table->date('approved_date')->nullable();
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
        Schema::dropIfExists('salary_generates');
    }
};
