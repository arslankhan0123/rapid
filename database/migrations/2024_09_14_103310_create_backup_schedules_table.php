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
        Schema::create('backup_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_name'); // Stores the frequency name (e.g., 'daily', 'weekly', etc.)
            $table->integer('frequency')->default(2); // Frequency (1: minute, 2: daily, 3: weekly, 4: monthly)
            $table->timestamp('last_backup_at')->nullable(); // To keep track of the last backup
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
        Schema::dropIfExists('backup_schedules');
    }
};
