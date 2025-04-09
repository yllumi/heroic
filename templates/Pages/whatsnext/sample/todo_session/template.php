<div id="todo" x-data="todo" x-debug>

    <?= $this->include('whatsnext/_navbar') ?>
        
    <div class="container mt-5">
        <div class="row">

            <div class="col-md-3">
                <?= $this->include('whatsnext/_sidebar') ?>
            </div>

            <div class="col-md-9">
                <h1 class="text-center mb-4">Todo List</h1>

                <p class="text-center">This todo app using pure Alpine.js, sending data using Axios and data save to PHP session.</p>

                <form @submit.prevent="addTodo" class="d-flex gap-2 mb-3">
                    <input type="text" class="form-control" placeholder="Add new todo" x-model="model.task">
                    <button class="btn btn-primary">Tambah</button>
                </form>

                <ul class="list-group">
                    <template x-for="(item,index) in data.todos" :key="item.id">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <input class="form-control me-2" x-model="data.todos[index].task" @change="updateTodo(index)">
                            <button class="btn btn-sm btn-danger" @click="deleteTodo(index)"><i class="bi bi-trash"></i></button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    Alpine.data('todo', () => {
        return {
            data: {},
            model: {},

            async init() {
                const res = await axios.get('/whatsnext/sample/todo_session/data');
                this.data = res.data;
                this.model.task = '';
            },

            async addTodo() {
                if (this.model.task.trim() === '') return;

                const formData = new FormData();
                formData.append('task', this.model.task);

                const res = await axios.post('/whatsnext/sample/todo_session/save', formData, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                this.data.todos.unshift(res.data.todo);
                this.model.task = '';
                $heroicHelper.toastr('Todo berhasil ditambahkan', 'success');
            },

            async updateTodo(index) {
                const confirmed = await Prompts.confirm("Lanjutkan pembaharuan?");
                if (confirmed) {
                    const formData = new FormData();
                    formData.append('task', JSON.stringify(this.data.todos[index]));

                    const res = await axios.post('/whatsnext/sample/todo_session/update', formData, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    $heroicHelper.toastr('Todo berhasil diperbarui', 'success');
                }
            },

            async deleteTodo(index) {
                const confirmed = await Prompts.confirm("Anda yakin akan menghapus?");
                if (confirmed) {
                    const formData = new FormData();
                    formData.append('id', this.data.todos[index].id);

                    const res = await axios.post('/whatsnext/sample/todo_session/delete', formData, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    this.data.todos.splice(index, 1);
                    $heroicHelper.toastr('Todo berhasil dihapus', 'success');
                }
            }
        }
    });
</script>
