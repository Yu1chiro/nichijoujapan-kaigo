<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('decks', function (Blueprint $table) {
            // Menambahkan kolom kategori dengan default 'Kaigo'
            $table->string('category')->default('Kaigo')->after('title');
        });
    }

    public function down(): void {
        Schema::table('decks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};