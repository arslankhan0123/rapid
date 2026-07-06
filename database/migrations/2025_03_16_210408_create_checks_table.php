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
        Schema::create('checks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('check_number')->unique(); // Unique check number
            $table->string('issue_name'); // Name on the check
            $table->decimal('amount', 10, 2); // Amount with precision for decimal values
            $table->unsignedBigInteger('branch_id'); // Foreign key for branch
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
        Schema::dropIfExists('checks');
    }
};
