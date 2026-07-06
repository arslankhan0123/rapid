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
        Schema::create('employee_quotations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estimate_id');
            $table->bigInteger('employee_id');
            $table->float('hours');
            $table->float('rate');
            $table->float('taxes');
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
        Schema::dropIfExists('employee_quotations');
    }
};
