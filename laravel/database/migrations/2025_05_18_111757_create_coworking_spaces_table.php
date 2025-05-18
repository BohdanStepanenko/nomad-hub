<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coworking_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('hours');
            $table->decimal('cost', 8, 2);
            $table->string('wifi_speed')->nullable();
            $table->boolean('has_coffee')->default(false);
            $table->boolean('is_24_7')->default(false);
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coworking_spaces');
    }
};
