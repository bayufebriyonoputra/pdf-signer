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
            $table->float('x_coor')->after('status');
            $table->float('y_coor')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_pos', function (Blueprint $table) {
            $table->dropIfExists('x_coor');
            $table->dropIfExists('y_coor');
        });
    }
};
