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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('datetime')->nullable();
            $table->text('error')->nullable();
            $table->string('file')->nullable();
            $table->integer('line')->default(0);
            $table->text('url')->nullable();
            $table->string('method')->nullable();
            $table->text('request')->nullable();
            $table->text('header')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('error_check')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
