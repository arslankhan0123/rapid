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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id'); // Add this line for the foreign key
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('leave_id'); // Add this line for the foreign key
            $table->foreign('leave_id')->references('id')->on('leaves');
            
            $table->date('from_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->string('hard_copy')->nullable();
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
        Schema::dropIfExists('leave_applications');
    }
};
