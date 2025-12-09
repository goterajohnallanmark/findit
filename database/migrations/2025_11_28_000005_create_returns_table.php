<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->nullable()->constrained('lost_items')->nullOnDelete();
            $table->foreignId('found_item_id')->nullable()->constrained('found_items')->nullOnDelete();
            $table->foreignId('returned_by')->constrained('users')->cascadeOnDelete();
            $table->date('return_date');
            $table->string('return_location');
            $table->string('return_method');
            $table->string('contact_info');
            $table->text('notes')->nullable();
            $table->string('return_photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
