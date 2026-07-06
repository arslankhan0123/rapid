<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('product_group_id');
            $table->foreign('product_group_id')->references('id')->on('item_groups');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('items');
            $table->double('budget')->nullable();
            $table->enum('priority', ['High', 'Medium', 'Low']);
            $table->date('start_date')->nullable();
            $table->unsignedInteger('assignee');
            $table->foreign('assignee')->references('id')->on('users')->onUpdate('cascade');
            $table->string('contact');
            $table->string('position')->nullable();
            $table->unsignedInteger('source_id');
            $table->foreign('source_id')->references('id')->on('lead_sources')->onUpdate('cascade');
            $table->integer('employees')->nullable();
            $table->integer('branches')->nullable();
            $table->integer('business');
            $table->boolean('automation')->default(false);
            $table->unsignedInteger('status_id');
            $table->foreign('status_id')->references('id')->on('lead_statuses')->onUpdate('cascade');
            $table->string('default_language')->nullable();
            $table->string('mobile');
            $table->string('whatsapp')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('country_id');
            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')->references('id')->on('areas')->onUpdate('cascade');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('inserted_by');
            $table->foreign('inserted_by')->references('id')->on('users')->onUpdate('cascade');
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
        Schema::drop('leads');
    }
};
