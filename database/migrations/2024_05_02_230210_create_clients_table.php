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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->foreignId('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->foreignId('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->unsignedBigInteger('client_mikrowisp_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
