<?php namespace App\Pages\notfound;

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "Page Notfound"
    ];

    public function getData()
    {
        return $this->respond([
			'response_code'    => 200,
			'response_message' => 'success',
			'data'             => $this->data
		]);
    }
}
