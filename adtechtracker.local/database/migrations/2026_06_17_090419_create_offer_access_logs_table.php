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
        Schema::create('offer_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->references('id')->on('offers')->cascadeOnDelete()->nullable();
            $table->foreignId('webmaster_id')->references('id')->on('users')->nullOnDelete()->nullable();
            $table->foreignId('subscription_id')->references('id')->on('offer_subscriptions')->nullOnDelete()->nullable();
            $table->string('ref_code')->nullable();
            $table->text('target_url')->nullable();
            $table->enum('status', ['allowed', 'rejected']);
            $table->string('reason')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['offer_id', 'created_at']);
            $table->index(['webmaster_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_access_logs');
    }
};
