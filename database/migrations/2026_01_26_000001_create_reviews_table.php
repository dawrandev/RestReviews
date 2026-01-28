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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('device_id', 100)->index()->comment('Unique device identifier');
            $table->string('ip_address', 45)->nullable()->comment('IP address (IPv4/IPv6)');
            $table->tinyInteger('rating')->unsigned()->comment('1-5 rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Ensure one review per device per restaurant
            $table->unique(['device_id', 'restaurant_id'], 'unique_device_restaurant');

            // Indexes for performance
            $table->index('restaurant_id');
            $table->index('rating');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
