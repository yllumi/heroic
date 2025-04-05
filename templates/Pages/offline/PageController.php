<?php namespace App\Pages\offline;

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "Offline Page"
    ];

    public function getData()
    {
        $this->data['name'] = "Kristina Bosco Jr.";

        return $this->respond([
		'data' => $this->data
	]);
    }
}
