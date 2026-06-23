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
        Schema::create('offer_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->references('id')->on('offers');
            $table->foreignId('webmaster_id')->references('id')->on('users');
            $table->string('ref_code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_subscriptions');
    }
};
