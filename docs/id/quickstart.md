# Mulai Cepat

Bagian ini akan membantu Anda memulai dengan Heroic dalam hitungan menit. Cukup ikuti langkah-langkah berikut untuk menyiapkan proyek Anda dan langsung mengembangkan halaman interaktif dengan cepat.

## Persyaratan Sistem
Sebelum memulai, pastikan Anda telah memiliki:

- PHP versi 8.1 atau lebih tinggi
- Composer
- CodeIgniter 4 (minimal versi 4.3)
- Web server lokal (seperti Apache, Nginx, atau bawaan CodeIgniter `php spark serve`)

## Instalasi CodeIgniter 4

Jalankan perintah berikut bila Anda belum memiliki project CodeIgniter 4

```bash
composer create-project codeigniter4/appstarter myapp
cd myapp
```

## Instalasi Heroic

Install Heroic melalui Composer dan jalankan perintah untuk instalasi:

```bash
composer require yllumi/heroic
php spark heroic:install
```

Perintah ini akan menambahkan folder dan file berikut ke dalam proyek Anda:

- `app/Pages/`
- `app/Pages/BaseController.php`
- `app/Pages/layout.php`
- `app/Pages/Router.php`
- `app/Views/layouts/default.php`
- Folder contoh halaman: `home/`, `notfound/`, `whatsnext/`
- File JS: `public/vendor/heroic/heroic.min.js`

## Menjalankan Aplikasi

Jalankan server lokal Anda:

```bash
php spark serve
```

Buka browser dan akses `http://localhost:8080`. Anda akan melihat halaman default Heroic berhasil dimuat menggunakan sistem routing berbasis Pinecone dan AlpineJS.

Heroic tidak menghapus mekanisme routing bawaan dari CodeIgniter 4. Sehingga untuk menggunakan tampilan homepage dari Heroic, kamu perlu menghapus atau menonaktifkan routing untuk homepage atau '/' bawaan CodeIgniter di app/Config/Routes.php.

## Struktur Halaman
Heroic memanfaatkan struktur berbasis folder. Misalnya, halaman `home` akan berada di:

```
app/Pages/home/PageController.php
app/Pages/home/template.php
```

Anda bisa menyalin folder `home` untuk membuat halaman baru atau gunakan perintah berikut:

```bash
php spark heroic:createPage nama_halaman
```

Perintah ini akan otomatis membuat struktur dan file dasar yang dibutuhkan.
