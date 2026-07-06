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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string("name");

            $table->unsignedInteger('department_id'); // Add this line for the foreign key
            $table->foreign('department_id')->references('id')->on('departments');
            $table->unsignedBigInteger('sub_department_id');
            $table->foreign('sub_department_id')->references('id')->on('sub_departments');
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id')->references('id')->on('designations');

            $table->date('dob')->nullable();
            $table->date('join_date');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->enum("gender", ['male', 'female', 'others'])->nullable();
            $table->enum("marital_status",['married', 'unmarried', 'divorced'])->nullable();
            $table->enum("blood_group",['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-','O+', 'O-'])->nullable();
            $table->string('religion')->nullable();
            $table->string('national_id')->nullable();
            $table->string('iqama_no')->nullable();
            $table->string('passport')->nullable();
            $table->string('driving_license_no')->nullable();


            $table->string('type')->nullable();
            $table->string('duty_type')->nullable();
            $table->float('hourly_rate',8,2)->nullable();

            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('iban_num')->nullable();

            $table->float('basic_salary',8,2)->nullable();
            $table->float('transport_allowance')->nullable();
            $table->float('gross_salary')->nullable();

            $table->boolean('status')->default(true);
            $table->string('company_name')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
