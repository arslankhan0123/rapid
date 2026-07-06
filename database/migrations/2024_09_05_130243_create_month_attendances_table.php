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
        Schema::create('month_attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id');
            $table->integer('customer_id');
            $table->integer('project_id');
            $table->date('month');
            $table->float('overtime',8,2)->nullable();
            $table->float('net',8,1)->nullable();
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
        Schema::dropIfExists('month_attendances');
    }
};
