<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Income Categories (Kas Masuk)
            [
                'category_id' => 'KM001',
                'category_name' => 'Pendapatan Jasa Listrik',
                'transaction_type' => 'income',
                'description' => 'Penerimaan dari layanan jasa listrik',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KM002',
                'category_name' => 'Pendapatan Non Operasional',
                'transaction_type' => 'income',
                'description' => 'Penerimaan di luar operasional utama',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KM003',
                'category_name' => 'Transfer Dana dari Pusat',
                'transaction_type' => 'income',
                'description' => 'Penerimaan dana dari kantor pusat PLN',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KM004',
                'category_name' => 'Penerimaan Lain-lain',
                'transaction_type' => 'income',
                'description' => 'Penerimaan dari sumber lainnya',
                'created_by' => 'USR_UP3LGS_001',
            ],

            // Expense Categories (Kas Keluar)
            [
                'category_id' => 'KK001',
                'category_name' => 'Operasional Kantor',
                'transaction_type' => 'expense',
                'description' => 'Biaya operasional harian kantor',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK002',
                'category_name' => 'Pemeliharaan Jaringan',
                'transaction_type' => 'expense',
                'description' => 'Biaya pemeliharaan infrastruktur jaringan listrik',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK003',
                'category_name' => 'Transport & Perjalanan Dinas',
                'transaction_type' => 'expense',
                'description' => 'Biaya transportasi dan perjalanan dinas',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK004',
                'category_name' => 'ATK dan Supplies',
                'transaction_type' => 'expense',
                'description' => 'Alat tulis kantor dan perlengkapan',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK005',
                'category_name' => 'Konsumsi dan Rapat',
                'transaction_type' => 'expense',
                'description' => 'Biaya konsumsi dan kegiatan rapat',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK006',
                'category_name' => 'Pembelian Peralatan',
                'transaction_type' => 'expense',
                'description' => 'Pembelian peralatan dan material',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK007',
                'category_name' => 'Biaya Listrik dan Utilitas',
                'transaction_type' => 'expense',
                'description' => 'Pembayaran listrik, air, telepon',
                'created_by' => 'USR_UP3LGS_001',
            ],
            [
                'category_id' => 'KK008',
                'category_name' => 'Pengeluaran Lain-lain',
                'transaction_type' => 'expense',
                'description' => 'Pengeluaran untuk keperluan lainnya',
                'created_by' => 'USR_UP3LGS_001',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}