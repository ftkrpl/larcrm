<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->enum('branch', [
                'Sidoarjo',
                'Jakarta',
                'Bandung',
                'Semarang',
                'Yogyakarta',
                'Malang',
                'Denpasar',
                'Makassar',
                'Jember',
                'Gift Shop',
                'Holding',
            ]);

            // tetap CHAR
            $table->char('customerid', 8);

            // CHAR → VARCHAR
            $table->string('extrefnbr', 20)->nullable();
            $table->string('customername', 50)->nullable();

            $table->enum('status', [
                'Active',
                'On Hold',
                'Credit Hold',
                'On-Time',
                'Inactive',
            ])->default('Active');

            $table->string('addressline1', 50)->nullable();
            $table->string('addressline2', 50)->nullable();

            $table->enum('priceclass', [
                'Dist',
                'Ecom',
                'FS',
                'Grosir',
                'GT',
                'MT',
                'NKA',
                'Perwakilan',
            ])->nullable();

            $table->string('tipe1', 20)->nullable();
            $table->string('tipe2', 20)->nullable();
            $table->string('tipe3', 20)->nullable();
            $table->string('admar', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
