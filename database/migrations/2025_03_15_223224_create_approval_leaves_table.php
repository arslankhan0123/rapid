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
        Schema::create('approval_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_application_id')->constrained()->onDelete('cascade'); // Reference to leave_application table
            $table->unsignedBigInteger('approved_by'); // User who approved
            $table->date('approved_date'); // Date of approval
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
        Schema::dropIfExists('approval_leaves');
    }
};
