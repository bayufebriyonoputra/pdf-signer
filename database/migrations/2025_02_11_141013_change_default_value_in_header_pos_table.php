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
        Schema::table('header_pos', function (Blueprint $table) {
            $table->foreignId('approver_2')->nullable()->change();
            $table->float('y_coor')->nullable()->change();
            $table->float('x_coor')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_pos', function (Blueprint $table) {
            $table->foreignId('approver_2')->change();
            $table->float('y_coor')->change();
            $table->float('x_coor')->change();
        });
    }
};
