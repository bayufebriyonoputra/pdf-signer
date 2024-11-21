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
        Schema::table('trackers', function (Blueprint $table) {
            $table->string('description')->after('message');
            $table->string('icon')->after('description');
            $table->string('additional_class')->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trackers', function (Blueprint $table) {
            $table->dropIfExists('description');
            $table->dropIfExists('icon');
            $table->dropIfExists('additional_class');
        });
    }
};
