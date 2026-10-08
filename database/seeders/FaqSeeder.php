<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara mengajukan retur barang?',
                'answer' => 'Isi formulir pengajuan retur di halaman utama dengan melengkapi data diri, nomor invoice, produk, alasan retur, dan unggah foto/video bukti. Anda akan menerima nomor tiket untuk memantau prosesnya.',
                'category' => 'Umum',
            ],
            [
                'question' => 'Berapa lama proses retur hingga refund selesai?',
                'answer' => 'Estimasi proses retur adalah 3-7 hari kerja sejak barang kami terima, tergantung hasil pemeriksaan gudang dan antrean proses refund.',
                'category' => 'Refund',
            ],
            [
                'question' => 'Bagaimana cara melacak status retur saya?',
                'answer' => 'Gunakan halaman Lacak Tiket dengan memasukkan nomor tiket yang Anda terima saat pengajuan, misalnya RET-20260101-0001.',
                'category' => 'Umum',
            ],
            [
                'question' => 'Apa saja syarat barang yang bisa diretur?',
                'answer' => 'Barang harus dalam kondisi sesuai saat diterima, disertai bukti foto/video yang jelas, dan pengajuan dilakukan maksimal 7 hari setelah barang diterima.',
                'category' => 'Produk',
            ],
            [
                'question' => 'Ke mana dana refund dikirim?',
                'answer' => 'Dana refund dikirim melalui metode yang dipilih oleh tim keuangan, umumnya transfer bank ke rekening pelanggan atau saldo e-wallet.',
                'category' => 'Refund',
            ],
            [
                'question' => 'Apakah ongkos kirim retur ditanggung toko?',
                'answer' => 'Untuk retur karena kesalahan toko (barang rusak/salah kirim), ongkos kirim ditanggung toko. Selain itu, ongkos kirim menjadi tanggung jawab pelanggan.',
                'category' => 'Pengiriman',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
