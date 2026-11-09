<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->index();
            $table->text('deskripsi')->nullable();
            $table->foreignId('departemen_id')
                ->constrained('departemen')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('dosen_id')
                ->nullable()
                ->constrained('dosen')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->date('tenggat')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
