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
        Schema::create('visit_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis', [
                'Regular Visit',
                'New Customer',
                'New Product Development',
                'Existing Product Offering',
                'Competitor Info',
                'Support',
                ])->nullable()->default(null);
            $table->string('kode_barang')->nullable();
            $table->string('kelompok_barang')->nullable();
            $table->enum('sample',['Yes','No'])->default('No');
            $table->enum('status',['Failed','Deal','etc'])->nullable()->default(null); // Failed, Deal, etc.
            $table->text('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_activities');
    }
};
