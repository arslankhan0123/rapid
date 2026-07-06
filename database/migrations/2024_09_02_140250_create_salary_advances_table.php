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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('permitted_by');
            $table->foreign('permitted_by')->references('id')->on('employees');
            $table->text('description')->nullable();
            $table->float('amount', 8, 2);
            $table->date('approved_date');
            $table->date('repayment_from');
            $table->float('interest_percentage');
            $table->integer('installment_period');
            $table->float('installment');
            $table->float('repayment_amount',8,2);
            $table->float('paid_amount',8,2)->nullable();
            $table->integer('paid_installment')->nullable();
            $table->enum('status', ['active', 'inactive','completed'])->default('active');
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
        Schema::dropIfExists('salary_advances');
    }
};
