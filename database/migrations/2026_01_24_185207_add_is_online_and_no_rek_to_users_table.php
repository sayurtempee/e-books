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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('isOnline')->default(false)->after('role');
            $table->string('no_rek', 20)->unique()->nullable()->after('role');
            $table->enum('bank_name', ['BCA', 'Mandiri', 'BNI', 'BRI'])->nullable()->after('no_rek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['isOnline', 'no_rek']);
        });
    }
};
