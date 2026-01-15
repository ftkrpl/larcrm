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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('kodesales',5);
            $table->string('kodecust',7);
            $table->string('kodeacum',8);
            $table->string('namacust',50);
            $table->string('namapic',50);
            $table->string('jabatanpic',25);
            $table->text('notes');           // Hasil kunjungan
            $table->date('visit_date');      // Tanggal berkunjung
            $table->timestamps();
        });
        DB::statement("
            ALTER TABLE visits
            ADD status SET(
                'Reguler Visit',
                'New Customer',
                'New Product Development',
                'Existing Product Offering',
                'Competitor Info'
            ) NULL AFTER notes
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
