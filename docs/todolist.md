# 3. Membuat Todo List Pertama

Pada bagian ini, kita akan membuat aplikasi **Todo List** sederhana menggunakan Heroic. Tutorial ini dirancang untuk membantu Anda memahami cara kerja struktur halaman, routing, integrasi AlpineJS, dan komunikasi data dalam Heroic.

---

## 3.1 Persiapan Halaman Todo

Jalankan perintah berikut untuk membuat halaman baru:

```bash
php spark heroic:createPage todo
```

Ini akan menghasilkan dua file utama:

```
app/Pages/todo/PageController.php
app/Pages/todo/template.php
```

Tambahkan route di file `app/Pages/router.php`:

```php
<?php

namespace App\Pages;

class Router
{
    public static array $router = [
        ...
        '/todo' => [],
    ];
}
```

> Saat Anda menjalankan perintah `php spark heroic:createPage todo`, route untuk halaman baru `todo` sudah otomatis ditambahkan. Kecuali Anda membuat folder halaman baru secara manual, maka Anda perlu menambahkan sendiri route halaman di file class ini.

---

## 3.2 Menyusun Tampilan Todo List

File `template.php` memiliki dua bagian utama seperti yang umum digunakan dalam pengembangan dengan AlpineJS:

1. **HTML** – berisi struktur elemen UI yang akan ditampilkan ke pengguna.
2. **Script** – berupa fungsi JavaScript (biasanya didefinisikan di bawah menggunakan `<script>`) yang berisi data dan metode yang digunakan dalam `x-data()`.

Struktur ini memisahkan logika dan tampilan dalam satu file secara praktis.

Edit file `template.php` agar menjadi seperti berikut:

```php
<div x-data='todoApp()' class="container py-5">
  <h1 class="mb-4 fw-bold">📋 Todo List</h1>

  <form @submit.prevent="addTask" class="row g-2 mb-3">
    <div class="col-9">
      <input x-model="newTask" type="text" placeholder="Tugas baru..." class="form-control" />
    </div>
    <div class="col-3 d-grid">
      <button type="submit" class="btn btn-primary">Tambah</button>
    </div>
  </form>

  <ul class="list-group">
    <template x-for="(task, index) in tasks" :key="index">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span x-text="task"></span>
        <button @click="removeTask(index)" class="btn btn-sm btn-outline-danger">Hapus</button>
      </li>
    </template>
  </ul>
</div>

<script>
function todoApp() {
  return {
    newTask: '',
    tasks: [],

    addTask() {
      if (this.newTask.trim() !== '') {
        this.tasks.push(this.newTask.trim());
        this.newTask = '';
      }
    },

    removeTask(index) {
      this.tasks.splice(index, 1);
    }
  }
}
</script>
```

Buka halaman http://localhost:8080/todo dan Anda sudah memiliki tampilkan aplikasi Todo sederhana. 

---

## 3.3 Menyimpan dan Menampilkan Data

Pada tahap ini, data masih disimpan di sisi klien menggunakan AlpineJS. Anda dapat menambahkan penyimpanan lokal (misalnya `localStorage`) jika ingin data tetap ada setelah reload halaman. 

Contoh dengan `localStorage`:

Tambahkan fungsi `init()` pada `todoApp()`:

```js
init() {
  this.tasks = JSON.parse(localStorage.getItem('tasks') || '[]');
},
```

Lalu ganti fungsi `addTask()` dan `removeTask()` agar menjadi seperti ini:

```js
addTask() {
  if (this.newTask.trim() !== '') {
    this.tasks.push(this.newTask.trim());
    this.newTask = '';
    localStorage.setItem('tasks', JSON.stringify(this.tasks));
  }
},

removeTask(index) {
  this.tasks.splice(index, 1);
  localStorage.setItem('tasks', JSON.stringify(this.tasks));
},
```

Sekarang data todo akan disimpan di localStorage dan fungsi `init()` akan mengambil data dari localStorage setiap kali halaman dimuat.

---


### 3.4 Menyimpan Todo di Database

Gunakan query berikut untuk membuat tabel:

```sql
CREATE TABLE todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Pastikan koneksi database Anda sudah dikonfigurasi di `.env` atau `Config/Database.php`.

---

#### Mengupdate `PageController.php`

Tambahkan dua method untuk mengambil dan menyimpan data:

```php
use CodeIgniter\Database\BaseBuilder;

public function getData()
{
    $db = db_connect();
    $query = $db->table('todos')->orderBy('id', 'desc')->get();
    $tasks = array_map(fn($row) => $row->task, $query->getResult());

    return $this->respond(['data' => $tasks]);
}

public function postData()
{
    $data = $this->request->getJSON();

    if (!isset($data->task)) {
        return $this->failValidationErrors('Task tidak boleh kosong.');
    }

    $db = db_connect();
    $db->table('todos')->insert([ 'task' => $data->task ]);

    return $this->respond(['status' => 'ok']);
}

public function postDelete()
{
    $data = $this->request->getJSON();

    if (!isset($data->task)) {
        return $this->failValidationErrors('Task harus disediakan.');
    }

    $db = db_connect();
    $db->table('todos')->where('task', $data->task)->delete();

    return $this->respond(['status' => 'deleted']);
}
```

Method getData() adalah endpoint untuk GET localhost:8080/todo/data, dan method postData() adalah endpoint untuk POST localhost:8080/todo/data. Anda dapat membuat method endpoint lainnya dengan menggunakan prefix get atau post sesuai HTTP method yang ingin digunakan. Method lain yang tidak menggunakan prefix tidak akan menjadi endpoint dan hanya dapat dipanggil di internal aplikasi. 

---

#### Mengupdate `template.php`

Ubah di bagian `<script>`, ganti fungsi `todoApp()` seperti ini:

```html
<script>
function todoApp() {
  return {
    newTask: '',
    tasks: [],

    async init() {
      const res = await fetch('/todo/data');
      const json = await res.json();
      this.tasks = json.data;
    },

    async addTask() {
      if (this.newTask.trim() === '') return;

      this.tasks.unshift(this.newTask);

      await fetch('/todo/data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task: this.newTask })
      });

      this.newTask = '';
    },

    async removeTask(index) {
      const taskToRemove = this.tasks[index];
      this.tasks.splice(index, 1);

      await fetch('/todo/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task: taskToRemove })
      });
    }
  }
}
</script>
```

---

## 4. Penutup

Dengan langkah ini, data Todo Anda kini disimpan ke dalam database, bukan hanya di browser atau localStorage. Anda dapat mengembangkan lebih jauh dengan fitur hapus, edit, atau filter berdasarkan waktu.

> 💡 Lanjutkan ke bagian [CRUD Lengkap dengan Heroic](./todo-crud.md) untuk menyempurnakan aplikasi Todo ini.


## 3.4 Penjelasan Struktur dan Mekanisme

- **`layout.php`** akan me-load `layouts/default.php` sebagai shell utama.
- **`#app`** di layout akan diganti dengan halaman `template.php` melalui AJAX saat route diaktifkan.
- **`BaseController`** mengatur method `getIndex()` dan `getTemplate()` sebagai dasar pemuatan halaman.
- **Pinecone Router** mengelola routing antar halaman secara client-side tanpa reload.
- **AlpineJS** bertugas menangani reaktivitas dan interaksi pengguna langsung di HTML.

---

> ✅ Todo List Anda kini sudah berfungsi! Lanjutkan ke [Routing dengan Pinecone](./04-routing.md) untuk memahami sistem routing yang digunakan Heroic.
