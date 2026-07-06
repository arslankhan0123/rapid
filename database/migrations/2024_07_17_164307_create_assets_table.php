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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('asset_category_id')->nullable();
            $table->foreign('asset_category_id')->references('id')->on('asset_categories');
            $table->string("company_name")->nullable();
            $table->string("company_asset_code")->nullable();
            $table->dateTime('purchase_date');
            $table->string('manufacturer');
            $table->string('serial_number')->nullable();
            $table->enum('is_working',['yes','no'])->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->dateTime('warranty_end_date');
            $table->string('invoice_number');
            $table->text('asset_note')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('assets');
    }
};
