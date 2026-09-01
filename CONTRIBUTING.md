# Kontribusi ke CRM Retur

Terima kasih sudah tertarik untuk berkontribusi. Panduan singkat ini menjelaskan cara ikut mengembangkan CRM Retur.

## Persiapan lingkungan

1. Fork repositori dan clone ke mesin lokal.
2. Ikuti langkah instalasi di [README](README.md#instalasi).
3. Pastikan `php artisan test` lolos seluruhnya sebelum membuat PR.

## Alur kontribusi

1. Buat branch baru dari `main` — contoh: `feat/export-csv` atau `fix/email-notification`.
2. Commit dengan pesan yang jelas dalam bahasa Indonesia atau Inggris.
3. Buka Pull Request ke branch `main`.
4. Jelaskan perubahan, alasan, dan cara mengujinya di deskripsi PR.

## Standar kode

- **PHP 8.2+** — gunakan typed properties, enum, match expression, dan fitur modern lainnya.
- **Laravel Pint** — jalankan `vendor/bin/pint` sebelum commit untuk menjaga konsistensi gaya kode.
- **Pengujian** — tambahkan atau perbarui pengujian fitur untuk setiap perubahan logika bisnis.
- **Penamaan** — ikuti konvensi Laravel: `PascalCase` untuk class, `snake_case` untuk kolom database dan route.

## Melaporkan bug

Buka [Issue](../../issues) baru dengan informasi:
- Langkah reproduksi.
- Perilaku yang diharapkan vs yang terjadi.
- Versi PHP, OS, dan browser (jika relevan).

## Mengusulkan fitur

Buka Issue atau [Discussion](../../discussions) baru. Jelaskan masalah yang ingin diselesaikan, bukan hanya solusinya. Diskusi terlebih dahulu membantu menghindari pekerjaan yang sia-sia.

## Lisensi

Dengan berkontribusi, kamu menyetujui bahwa kontribusimu dilisensikan di bawah [MIT License](LICENSE) yang sama dengan proyek ini.
