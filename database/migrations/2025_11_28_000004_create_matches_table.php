<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained('lost_items')->cascadeOnDelete();
            $table->foreignId('found_item_id')->constrained('found_items')->cascadeOnDelete();
            $table->decimal('similarity_score', 5, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['lost_item_id', 'found_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
