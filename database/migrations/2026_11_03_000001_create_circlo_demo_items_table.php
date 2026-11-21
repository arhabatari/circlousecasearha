<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circlo_demo_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 50);
            $table->string('owner', 100);
            $table->unsignedInteger('daily_price');
            $table->string('status', 20)->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circlo_demo_items');
    }
};
