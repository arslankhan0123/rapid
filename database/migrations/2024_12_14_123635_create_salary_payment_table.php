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
        Schema::create('salary_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_sheet_id');
            $table->enum('payment_type', ['cash', 'bank'])->default('cash');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('salary_sheet_id')->references('id')->on('salary_sheets');
            $table->foreign('bank_id')->references('id')->on('banks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_payment');
    }
};
