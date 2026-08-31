<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Diajukan = 'Diajukan';
    case Diverifikasi = 'Diverifikasi';
    case Disetujui = 'Disetujui';
    case Ditolak = 'Ditolak';
    case MenungguBarang = 'Menunggu Barang';
    case BarangDiterima = 'Barang Diterima';
    case PemeriksaanGudang = 'Pemeriksaan Gudang';
    case RefundDiproses = 'Refund Diproses';
    case Selesai = 'Selesai';

    /**
     * Label yang ditampilkan ke pengguna.
     */
    public function label(): string
    {
        return $this->value;
    }

    /**
     * Kelas warna badge Tailwind untuk setiap status.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Diajukan => 'bg-slate-100 text-slate-700',
            self::Diverifikasi => 'bg-sky-100 text-sky-700',
            self::Disetujui => 'bg-blue-100 text-blue-700',
            self::Ditolak => 'bg-red-100 text-red-700',
            self::MenungguBarang => 'bg-amber-100 text-amber-700',
            self::BarangDiterima => 'bg-orange-100 text-orange-700',
            self::PemeriksaanGudang => 'bg-violet-100 text-violet-700',
            self::RefundDiproses => 'bg-indigo-100 text-indigo-700',
            self::Selesai => 'bg-emerald-100 text-emerald-700',
        };
    }

    /**
     * Apakah status ini termasuk status akhir (terminal)?
     */
    public function isTerminal(): bool
    {
        return $this === self::Selesai || $this === self::Ditolak;
    }

    /**
     * Urutan alur kerja utama (Ditolak berada di luar jalur utama).
     */
    public function order(): int
    {
        return match ($this) {
            self::Diajukan => 1,
            self::Diverifikasi => 2,
            self::Disetujui => 3,
            self::MenungguBarang => 4,
            self::BarangDiterima => 5,
            self::PemeriksaanGudang => 6,
            self::RefundDiproses => 7,
            self::Selesai => 8,
            self::Ditolak => 9,
        };
    }
}
