<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('management_id')->unique();
            $table->string('title');
            $table->text('comment')->nullable();
            $table->string('image_path')->nullable();
            $table->string('sold_image_path')->nullable();
            $table->string('status')->default('selling');
            $table->unsignedInteger('purchase_price')->default(0);
            $table->unsignedInteger('sold_price')->nullable();
            $table->integer('profit')->nullable();
            $table->date('sold_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_items');
    }
};