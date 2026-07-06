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
        Schema::create('salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // Add this line for the foreign key
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('salary_generate_id'); // Add this line for the foreign key
            $table->foreign('salary_generate_id')->references('id')->on('salary_generates');

            $table->float('basic_salary', 8, 2);
            $table->float('salary_advance', 8, 2);
            $table->float('gross_salary', 8, 2);
            $table->float('state_income_tax', 8, 2)->default(0);
            $table->float('loan', 8, 2)->default(0);
            $table->float('total_bonus', 8, 2)->default(0);
            $table->float('total_allowances', 8, 2)->default(0);
            $table->float('total_commission', 8, 2)->default(0);
            $table->float('total_insurance', 8, 2)->default(0);
            $table->float('total_deduction', 8, 2)->default(0);
            $table->float('net_salary', 8, 2);
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
        Schema::dropIfExists('salary_sheets');
    }
};
