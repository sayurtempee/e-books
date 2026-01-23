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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('photos_product');
            $table->string('title');
            $table->integer('stock')->default(0);
            $table->string('unit', 20);
            $table->text('description');
            $table->decimal('capital', 10, 2); // harga modal
            $table->decimal('price', 10, 2);   // harga jual
            $table->decimal('margin', 5, 2)->nullable(); // hanya info
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
