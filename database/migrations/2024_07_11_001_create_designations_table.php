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
        Schema::create('designations', function (Blueprint $table) {
            $table->id(); // Big integer primary key auto-incrementing
            $table->string('name'); // String column with varchar(191)
            $table->text('description')->nullable(); // Text column, nullable
            $table->unsignedInteger('department_id')->nullable(); // Add department_id column
            $table->unsignedBigInteger('sub_department_id')->nullable(); // Add sub_department_id column
            $table->timestamps(); // Created at and updated at timestamps
            // Define the foreign key constraints separately
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('sub_department_id')->references('id')->on('sub_departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designations');
    }
};
