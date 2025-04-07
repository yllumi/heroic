# Membuat Todo List Pertama

Pada bagian ini, kita akan membuat aplikasi **Todo List** sederhana menggunakan Alpine.js. Tutorial ini dirancang untuk membantu Anda memahami cara kerja struktur halaman, routing, integrasi Alpine.js, dan komunikasi data dalam Heroic.

---

## Persiapan Halaman Todo

Jalankan perintah berikut untuk membuat halaman baru:

```bash
php spark heroic:createPage todo
```

Ini akan menghasilkan dua file utama:

```
app/Pages/todo/PageController.php
app/Pages/todo/template.php
```

---

## Menyusun Tampilan Todo List

File `template.php` memiliki dua bagian utama seperti yang umum digunakan dalam pengembangan dengan Alpine.js:

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

## Menyimpan dan Menampilkan Data

Pada tahap ini, data masih disimpan di sisi klien menggunakan Alpine.js. Anda dapat menambahkan penyimpanan lokal (misalnya `localStorage`) jika ingin data tetap ada setelah reload halaman. 

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
