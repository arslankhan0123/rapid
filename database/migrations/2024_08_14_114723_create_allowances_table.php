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
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // Add this line for the foreign key
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('allowance_type_id'); // Add this line for the foreign key
            $table->foreign('allowance_type_id')->references('id')->on('allowance_types');
            $table->string('amount');
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
        Schema::dropIfExists('allowances');
    }
};
