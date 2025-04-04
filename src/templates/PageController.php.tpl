<?php namespace App\Pages\{{pageNamespace}};

use App\Pages\BaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends BaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "{{pageName}} Page"
    ];

    public function getData()
    {
        $this->data['name'] = "{{fakerName}}";

        return $this->respond([
		'data' => $this->data
	]);
    }
}
