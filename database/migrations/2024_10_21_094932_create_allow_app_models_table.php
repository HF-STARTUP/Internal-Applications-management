<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('allow_app_models', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dateTime');
            $table->string('userID');
            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->string('appID');
            $table->foreign('appID')->references('appID')->on('app_models')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allow_app_models');
    }
};
