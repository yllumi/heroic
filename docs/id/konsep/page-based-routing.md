# Routing Halaman

Heroic menggunakan package `yllumi/ci4-pages` sebgai dependensi package untuk menyediakan sistem routing berbasis folder yang memudahkan pengelolaan halaman di aplikasi CodeIgniter 4. Konsep ini bertujuan menyederhanakan struktur aplikasi berbasis halaman, tanpa perlu mendaftarkan satu per satu route secara manual di file **app/Config/Routes.php**.

Meski demikian, Anda tetap dapat menggunakan secara bersamaan mekanisme routing bawaan CodeIgniter 4 dengan routing halaman dari Heroic.

---

## Konsep Dasar

Setiap folder yang ada di dalam direktori **app/Pages/** dianggap sebagai sebuah halaman. Package ini secara otomatis mencocokkan URL dengan struktur folder dan file di direktori tersebut.

Contoh:

```
app/Pages/
├── home/
│   ├── PageController.php
│   └── template.php
├── about/
│   ├── PageController.php
│   ├── template.php
│   ├── company/
│   │   ├── PageController.php
│   │   └── template.php
```

Maka halaman yang bisa diakses adalah:

- *http://localhost/home* → ditangani oleh `app/Pages/home/PageController.php`
- *http://localhost/about* → ditangani oleh `app/Pages/about/PageController.php`
- *http://localhost/about/company* → ditangani oleh `app/Pages/about/company/PageController.php`

---

## Struktur Halaman

Setiap halaman berada dalam satu folder yang terdiri dari minimal dua file:

- `PageController.php` — controller untuk halaman, **WAJIB** bernama `PageController` dan extend dari `App\Pages\BaseController`
- `template.php` — view utama halaman yang akan dirender

Selain dua file di atas, Anda dapat membuat file lain seperti class atau parsial view sesuai kebutuhan.

---

## Method Controller

Mekanisme routing halaman mengharuskan Anda untuk menggunakan **prefix nama method** pada controller untuk menentukan jenis HTTP method yang diizinkan untuk mengakses fungsi tersebut. Dengan kata lain, metode dalam controller harus diawali dengan prefix `get`, `post`, `put`, atau `delete` agar dapat dikenali sebagai endpoint yang valid.

Misalkan, untuk halaman */about*, controller **app/Pages/about/PageController.php** dapat memiliki:

- `getIndex()` → dijalankan saat user mengakses halaman */about* dengan metode GET
- `postIndex()` → dijalankan saat halaman menerima request POST ke route */about*
- `postSave()` → dijalankan saat halaman menerima request POST ke route */about/save*
- `deleteItem()` → dipanggil saat ada request DELETE ke route */about/item*

> Penggunaan prefix ini sama seperti mekanisme autoroute (improved) yang disediakan oleh CodeIgniter 4.

Contoh penggunaan pada controller:

```php
namespace App\Pages\about;

use App\Pages\BaseController;

class PageController extends BaseController
{
    public function getIndex()
    {
        return pageView('about/template');
    }
}
```

Alih-alih menggunakan `view()`, Anda perlu menggunakan fungsi `pageView()` bila ingin memuat dan menampilkan file view yang ada di dalam **app/Pages/**. Adapun fungsi `view()` tetap dapat digunakan untuk menampilkan file view yang ada di dalam folder **app/Views/**.

---

## BaseController

Semua `PageController.php` perlu meng-extend `App\Pages\BaseController`, yang telah disediakan oleh Heroic saat proses instalasi. BaseController ini menyediakan integrasi otomatis dengan mekanisme routing dan layout yang disediakan oleh Heroic.

---

## Keuntungan dan Kekurangan Routing Berbasis Halaman

### Keuntungan

- **Lebih rapi dan terstruktur:** setiap halaman berdiri sendiri dalam folder
- **Tidak perlu daftar manual di `Routes.php`** — cukup buat folder dan controller
- **Mudah dikembangkan secara modular** — cocok untuk proyek yang besar atau tim

### Kekurangan

Meskipun praktis dan terstruktur, pendekatan folder-based memiliki beberapa keterbatasan:

- **Refactor lebih kompleks:** ketika Anda ingin mengganti URL atau memindahkan halaman ke path baru, Anda harus memindahkan folder beserta file controller dan view-nya. Hal ini bisa memengaruhi dependensi internal, misalnya include layout atau partial view.
- **URL terikat ke struktur folder:** Anda tidak bisa sepenuhnya mengatur URL secara bebas karena URL sangat tergantung pada nama folder dan struktur direktori.
- **Sulit untuk nested dynamic route:** jika Anda ingin membuat struktur URL yang sangat fleksibel dengan parameter kompleks, pendekatan ini bisa membatasi dibandingkan routing manual.

### Tips: Menyusun Struktur Folder Sejak Awal

Untuk meminimalisir kebutuhan refactor di kemudian hari:

- Rancang struktur halaman berdasarkan hirarki navigasi dan alur pengguna sejak awal proyek.
- Gunakan subfolder seperti `/admin/`, `/user/`, `/profile/` untuk memisahkan domain fitur.
- Hindari penamaan umum seperti `/page1`, `/test`, atau `/baru` saat development awal.
- Jika memungkinkan, diskusikan naming dan struktur URL bersama tim sebelum implementasi.

---

## Membuat dan Memindahkan Halaman

Anda dapat mulai membuat halaman baru dengan perintah `php spark heroic:createPage nama_halaman` atau `php spark heroic:createPage nama_halaman/subhalaman` yang otomatis menghasilkan struktur folder dan file sesuai standar Heroic.

Untuk memindahkan halaman, Anda dapat menggunakan perintah  `php spark heroic:createPage nama_lama nama_baru`, dimana sistem akan membantu memperbaharui namespace pada controller. Meski demikian, Anda tetap perlu mengecek kode Anda terutama terkait perubahan path ini.