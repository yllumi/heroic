<?php namespace App\Pages\whatsnext\sample\todo_session;

use App\Pages\BaseController;

class PageController extends BaseController 
{
    public $data = [
        'page_title' => "Todo List"
    ];

    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function getData()
    {
        $todos = $this->session->get('todos') ?? [];
        $this->data['todos'] = array_reverse($todos); // supaya yang terakhir muncul di atas

        return $this->respond($this->data);
    }

    public function postSave()
    {
        $postdata = $this->request->getPost();

        // Ambil todos lama dari session
        $todos = $this->session->get('todos') ?? [];

        // Buat ID baru
        $newId = count($todos) > 0 ? max(array_column($todos, 'id')) + 1 : 1;

        $newTodo = [
            'id' => $newId,
            'task' => $postdata['task']
        ];

        $todos[] = $newTodo;

        // Simpan lagi ke session
        $this->session->set('todos', $todos);

        $this->data['todo'] = $newTodo;

        return $this->respond($this->data);
    }

    public function postUpdate()
    {
        $postdata = $this->request->getPost('task');
        $todo = json_decode($postdata, true);

        $todos = $this->session->get('todos') ?? [];

        foreach ($todos as &$item) {
            if ($item['id'] == $todo['id']) {
                $item['task'] = $todo['task'];
                break;
            }
        }

        $this->session->set('todos', $todos);

        return $this->respond([
            'message' => 'Updated.'
        ]);
    }

    public function postDelete()
    {
        $id = (int)$this->request->getPost('id');

        $todos = $this->session->get('todos') ?? [];
        $todos = array_filter($todos, fn($item) => $item['id'] !== $id);

        // Reindex array
        $todos = array_values($todos);

        $this->session->set('todos', $todos);

        return $this->respond([
            'message' => 'Deleted'
        ]);
    }
}
