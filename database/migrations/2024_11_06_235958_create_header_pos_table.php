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
        Schema::create('header_pos', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->foreignId('approver_1')->nullable()->constrained('users','id')->cascadeOnDelete();
            $table->foreignId('approver_2')->constrained('users','id')->cascadeOnDelete();
            $table->enum('status',['new', 'check', 'sign', 'send', 'confirm']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_pos');
    }
};
