<?php namespace App\Pages\whatsnext\sample;

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    public $data = [
        'page_title' => "Whatsnext Sample Page"
    ];

    public function getData()
    {
        $this->data['name'] = "Dorcas Grimes";

        return $this->respond([
		'data' => $this->data
	]);
    }
}
