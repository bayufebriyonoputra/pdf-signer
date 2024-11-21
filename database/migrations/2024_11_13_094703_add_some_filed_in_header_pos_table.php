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
            $table->foreignId('supplier_id')->after('id')->constrained('suppliers', 'id')->cascadeOnDelete();
            $table->date('due_date')->after('status');
            $table->enum('jenis_transaksi',['barang', 'jasa'])->after('due_date');
            $table->string('pending_remark')->after('jenis_transaksi')->nullable();
            $table->string('additional_attachment')->after('pending_remark')->nullable();
            $table->boolean('is_remindered')->after('additional_attachment')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('header_pos', function (Blueprint $table) {
            $table->dropIfExists('supplier_id');
            $table->dropIfExists('due_date');
            $table->dropIfExists('jenis_transaksi');
            $table->dropIfExists('pending_remark');
            $table->dropIfExists('additional_attachment');
            $table->dropIfExists('is_remindered');
        });
    }
};
