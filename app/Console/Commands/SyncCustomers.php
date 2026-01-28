<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class SyncCustomers extends Command
{
    // Ini perintah yang akan dipanggil di terminal
    protected $signature = 'app:sync-customers';
    protected $description = 'Sinkronisasi data customer dari database eksternal';

    public function handle()
    {
        $this->info('Memulai sinkronisasi...');

        try {
            // 1. Ambil data dari database sebelah
            // Pastikan koneksi 'mysql_external' sudah diatur di config/database.php
            $externalData = DB::connection('mysql_external')->table('customer_table_asal')->get();

            $bar = $this->output->createProgressBar(count($externalData));
            $bar->start();

            // Definisi sekali di luar
            $clean = fn($value) => $value ? trim($value) : '';

            foreach ($externalData as $row) {
                Customer::updateOrCreate(
                    ['customerid' => $row->customerid_asal], // Kunci unik
                    [
                        'branch'       => $row->branch,
                        'extrefnbr'    => $clean($row->extrefnbr),
                        'customername' => $clean($row->customername),
                        'status'       => $row->status,
                        'addressline1' => $clean($row->addressline1),
                        'addressline2' => $clean($row->addressline2),
                        'priceclass'   => $row->priceclass,
                        'tipe1'        => $clean($row->tipe1),
                        'tipe2'        => $clean($row->tipe2),
                        'tipe3'        => $clean($row->tipe3),
                        'admar'        => $clean($row->admar),
                    ]
                );
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Sinkronisasi selesai dengan sukses!');

        } catch (\Exception $e) {
            $this->error('Gagal sinkronisasi: ' . $e->getMessage());
        }
    }
}