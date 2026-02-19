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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', ['tolak', 'pending', 'approved', 'shipping', 'selesai', 'refunded'])->default('pending');
            $table->string('tracking_number')->unique()->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->integer('qty');
            $table->decimal('capital', 10, 2);
            $table->decimal('price', 10, 2);
            $table->decimal('profit', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
