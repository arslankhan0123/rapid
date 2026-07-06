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
        Schema::create('monthly_attendance_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('project_id');
            $table->date('month');
            $table->bigInteger('posted_by');
            $table->bigInteger('updated_by');
            $table->date('posted_at');
            $table->integer('total_employees');
            $table->float('total_amount', 8, 2);
            $table->float('paid_amount', 8, 2);
            $table->enum('status', ['paid', 'unpaid', 'partially'])->default('unpaid');
            $table->float('total_hours', 8, 2)->nullable();
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
        Schema::dropIfExists('monthly_attendances');
    }
};
