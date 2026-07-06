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
        Schema::create('vat_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('period',['q1','q2','q3','q4']);
            $table->float('input',8,2);
            $table->float('output',8,2);
            $table->float('net',8,2)->nullable();
            $table->float('paid',8,2);
            $table->float('unpaid',8,2);
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
        Schema::dropIfExists('vat_reports');
    }
};
