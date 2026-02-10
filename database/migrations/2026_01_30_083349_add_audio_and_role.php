<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom untuk URL Audio final dari ImageKit
        Schema::table('questions', function (Blueprint $table) {
            $table->text('audio_url')->nullable()->after('thumbnail_url');
        });

        // 2. Tambah role user dengan default 'admin' 
        // agar perintah filament:user otomatis membuat admin
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('audio_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};