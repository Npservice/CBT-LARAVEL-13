
# CBT LARAVEL 13

Aplikasi Computer Based Test (CBT) berbasis web yang dikembangkan menggunakan framework Laravel 13. Project ini dirancang untuk mengelola dan melaksanakan ujian secara online dengan sistematis dan efisien.

## Cara Menjalankan Aplikasi

Aplikasi ini menggunakan prosedur standar Laravel. Ikuti langkah-langkah berikut untuk menjalankan project di lingkungan lokal Anda:

1. **Clone Repository**
```bash
git clone [https://github.com/Npservice/CBT-LARAVEL-13.git](https://github.com/Npservice/CBT-LARAVEL-13.git)
```
Kemudian

```
cd CBT-LARAVEL-13
```



2. **Install Dependensi Composer**
```bash
composer install

```


3. **Install Dependensi NPM** (Jika menggunakan frontend asset)
```bash
npm install && npm run dev

```


4. **Salin Konfigurasi Environment**
```bash
cp .env.example .env

```


*Buka file `.env` dan sesuaikan pengaturan database Anda (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

5. **Generate Application Key**
```bash
php artisan key:generate

```


6. **Jalankan Migrasi & Seeder Database**
```bash
php artisan migrate --seed

```


7. **Jalankan Server Lokal**
```bash
php artisan serve

```


Aplikasi dapat diakses melalui browser di alamat `http://127.0.0.1:8000`.

## Kontak & Laporan Bug

Jika Anda menemukan bug, kendala teknis, atau memiliki masukan mengenai aplikasi ini, silakan laporkan melalui email ke:
📩 **ridlonandaputra@gmail.com**

Terima kasih atas kontribusi Anda dalam pengembangan aplikasi ini!