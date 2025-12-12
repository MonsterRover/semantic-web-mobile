<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('abstrak')->nullable();
            $table->string('kata_kunci')->nullable();
            $table->string('topik')->nullable();
            $table->year('tahun');
            $table->string('penulis');
            $table->string('pembimbing')->nullable();
            $table->string('file_path')->nullable(); // Path to PDF/DOCX file
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable(); // pdf or docx
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for faster search
            $table->index('judul');
            $table->index('kata_kunci');
            $table->index('topik');
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi');
    }
};
