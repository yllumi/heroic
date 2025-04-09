<?php namespace App\Pages\todolist;

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "Todolist Page"
    ];

    public function getData()
    {
        $db = db_connect();
        $data['todolists'] = $db->table('todolists')
                                ->orderBy('id', 'desc')
                                ->get()
                                ->getResultArray();

        return $this->respond($data);
    }

    public function postIndex()
    {
        $task = $this->request->getPost('task');

        $db = db_connect();
        $db->table('todolists')->insert([
            'task' => $task
        ]);

        return $this->respond([
            'status' => 'success',
            'message' => 'Task berhasil ditambahkan.'
        ]);
    }

    public function putIndex($id)
    {
        $task = $this->request->getRawInputVar('task');

        $db = db_connect();
        $db->table('todolists')->where('id', $id)->update([
            'task' => $task
        ]);

        return $this->respond([
            'status' => 'success',
            'message' => 'Task berhasil diperbarui. - ' . $task
        ]);
    }

    public function deleteIndex($id)
    {
        $db = db_connect();
        $deleted = $db->table('todolists')->delete(['id' => $id]);

        if ($deleted) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Task berhasil dihapus.'
            ]);
        } else {
            return $this->failNotFound('Task tidak ditemukan.');
        }
    }
}
