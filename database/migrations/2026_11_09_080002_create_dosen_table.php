<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 18)->unique();
            // Kolom yang sering dicari diberi indeks agar pencarian tetap cepat
            $table->string('nama')->index();
            $table->string('email')->unique();
            $table->foreignId('departemen_id')
                ->constrained('departemen')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('jabatan', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
