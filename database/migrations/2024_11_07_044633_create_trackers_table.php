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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
            $table->string('no_po');
            // Menambahkan foreign key
            $table->foreign('no_po')
                  ->references('no_po') // Kolom yang dirujuk di tabel header_pos
                  ->on('header_pos') // Nama tabel yang dirujuk
                  ->cascadeOnDelete(); // Menentukan aksi saat data dihapus
            $table->string('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackers');
    }
};
