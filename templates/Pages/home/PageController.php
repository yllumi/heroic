<?php namespace App\Pages\home;

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "Home Page"
    ];

    public function getData()
    {
        $this->data['name'] = "Hubert Stracke";

        return $this->respond([
			'response_code'    => 200,
			'response_message' => 'success',
			'data'             => $this->data
		]);
    }
}
