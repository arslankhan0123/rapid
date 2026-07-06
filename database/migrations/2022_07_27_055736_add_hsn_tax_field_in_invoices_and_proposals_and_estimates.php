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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('hsn_tax')->nullable();
        });
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('hsn_tax')->nullable();
        });
        Schema::table('estimates', function (Blueprint $table) {
            $table->string('hsn_tax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('hsn_tax');
        });
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('hsn_tax');
        });
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('hsn_tax');
        });
    }
};
