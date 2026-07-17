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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel proses_produksis dan users
            // onDelete('cascade') memastikan jika data produksi atau user dihapus, log terkait ikut bersih
            $table->foreignId('proses_produksi_id')
                ->constrained('proses_produksis')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Inti riwayat perubahan
            $table->string('field_name', 255);       // Nama kolom yang dirubah (e.g., input, jtpcs)
            $table->text('old_value')->nullable();  // Nilai sebelum diedit
            $table->text('new_value')->nullable();  // Nilai baru hasil edit

            // Waktu kejadian: Hanya butuh created_at karena log tidak boleh diedit (immutable)
            $table->timestamp('created_at')->useCurrent();

            // INDEXING: Agar performa filter dan search di halaman log tetap cepat
            $table->index('created_at');
            $table->index(['proses_produksi_id', 'field_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
