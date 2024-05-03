<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_mikrotik_and_d_b_locals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->integer('client_mikrotik_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_mikrotik_and_d_b_locals');
    }
};