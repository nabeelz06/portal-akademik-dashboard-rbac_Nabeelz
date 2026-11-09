<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nrp', 10)->unique();
            // Kolom yang sering dicari diberi indeks agar pencarian tetap cepat
            $table->string('nama')->index();
            $table->string('email')->unique();
            $table->foreignId('departemen_id')
                ->constrained('departemen')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('dosen_wali_id')
                ->nullable()
                ->constrained('dosen')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->unsignedSmallInteger('angkatan');
            $table->decimal('ipk', 3, 2)->default(0);
            // true = aktif, false = cuti akademik
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
