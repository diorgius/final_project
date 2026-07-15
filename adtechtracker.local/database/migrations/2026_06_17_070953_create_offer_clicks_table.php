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
        Schema::create('offer_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->references('id')->on('offers');
            $table->foreignId('advertiser_id')->references('id')->on('users');
            $table->foreignId('webmaster_id')->references('id')->on('users');
            $table->foreignId('subscription_id')->references('id')->on('offer_subscriptions');
            $table->string('ref_code');
            $table->text('target_url');
            $table->decimal('advertiser_cost', 10, 2);      
            $table->decimal('webmaster_income', 10, 2);     
            $table->decimal('system_commission', 10, 2);
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['offer_id', 'created_at']);
            $table->index(['advertiser_id', 'created_at']);         
            $table->index(['webmaster_id', 'created_at']);
            $table->index(['subscription_id', 'created_at']);
            $table->index('ref_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_clicks');
    }
};
