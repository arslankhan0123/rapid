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
        Schema::create('new_attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id');
            $table->integer('customer_id');
            $table->integer('project_id');
            $table->date('date');
            $table->float('hours', 8, 2);
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
        Schema::dropIfExists('new_attendances');
    }
};
