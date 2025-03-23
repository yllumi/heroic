<?php namespace App\Pages\{{pageNamespace}};

use Yllumi\Heroic\Controllers\PageBaseController;
use CodeIgniter\API\ResponseTrait;

class PageController extends PageBaseController 
{
    use ResponseTrait;

    public $data = [
        'page_title' => "{{pageName}} Page"
    ];

    public function getData()
    {
        $this->data['name'] = "{{fakerName}}";

        return $this->respond([
			'response_code'    => 200,
			'response_message' => 'success',
			'data'             => $this->data
		]);
    }
}
