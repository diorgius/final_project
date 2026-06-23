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
            $table->foreignId('offer_id')->nullable()->references('id')->on('offers');
            $table->foreignId('webmaster_id')->nullable()->references('id')->on('users');
            $table->foreignId('subscription_id')->nullable()->references('id')->on('offer_subscriptions');
            $table->string('ref_code')->nullable();
            $table->text('target_url')->nullable();
            $table->enum('status', ['allowed', 'rejected']);
            $table->string('reason', 50)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['offer_id', 'created_at']);
            $table->index(['webmaster_id', 'created_at']);
            $table->index('ref_code');
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
