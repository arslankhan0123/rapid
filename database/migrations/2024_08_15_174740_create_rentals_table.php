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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id'); // Add this line for the foreign key
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type',['hourly','daily','monthly']);
            $table->float('amount',8,2);
            $table->unsignedInteger('tax_id'); // Add this line for the foreign key
            $table->foreign('tax_id')->references('id')->on('tax_rates');
            $table->float('tax_amount');
            $table->float('total_rent_amount');
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
        Schema::dropIfExists('rantals');
    }
};
