<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();                                              // kolom id otomatis
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // relasi ke users
            $table->string('title');                                   // judul task
            $table->text('description')->nullable();                   // deskripsi, boleh kosong
            $table->enum('status', ['pending', 'done'])->default('pending'); // status
            $table->date('due_date')->nullable();                      // deadline, boleh kosong
            $table->boolean('is_public')->default(false);              // flag untuk guest endpoint
            $table->timestamps();                                      // created_at & updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};