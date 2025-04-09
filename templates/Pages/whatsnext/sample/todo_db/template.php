<div 
    id="todolist" 
    x-data="$heroic({ 
        url: '/todolist/data', 
        title: 'Todo List',
        meta: {
            id: null,
            task: null
        }
    })" 
    x-debug>
    
    <div class="container mt-5">
        <h1>Todo List</h1>

        <!-- Form add task -->
        <form 
            class="mb-3" 
            x-data="$heroicForm({ 
                postUrl: '/todolist',
                onSuccess: (result) => {
                    reload(); 
                    $heroicHelper.toastr(result.message, 'success');
                } 
                })">
            <div class="input-group">
                <input type="text" name="task" class="form-control" placeholder="Tambahkan tugas baru..." required>
                <button class="btn btn-primary" type="submit">Tambah</button>
            </div>
        </form>

        <!-- Form Edit Task -->
        <div x-show="meta.id" class="mb-3">
            <form x-data="$heroicForm({ 
                        putUrl: () => `/todolist/${meta.id}`,
                        onSuccess: (res) => { 
                            reload(); 
                            $heroicHelper.toastr(res.message, 'success'); 
                            meta.id = null; 
                        }
                    })">
                <div class="input-group">
                    <input type="text" name="task" x-model="meta.task" class="form-control" required>
                    <button class="btn btn-success" type="submit">Update</button>
                    <button class="btn btn-secondary" type="button" @click="meta.id = null">Batal</button>
                </div>
            </form>
        </div>

        <ul class="list-group">
            <template x-for="(item, index) in data.todolists" :key="item.id">
                <li class="list-group-item d-flex justify-content-between">
                    <span x-text="item.task"></span>

                    <div>
                        
                        <button class="btn btn-sm btn-outline-primary" @click="meta.id = item.id; meta.task = item.task">
                            Edit
                        </button>

                        <form x-data="$heroicForm({ 
                                deleteUrl: () => `/todolist/${item.id}`,
                                confirm: true,
                                onSuccess: (res) => {
                                    reload();
                                    $heroicHelper.toastr(res.message, 'success');
                                }
                            })"
                            class="d-inline">
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                            Hapus
                            </button>
                        </form>
                    </div>
                </li>
            </template>
        </ul>
    </div>

</div>
