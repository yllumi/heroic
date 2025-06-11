<?php namespace App\Pages\{{pageNamespace}};

use App\Pages\BaseController;

class PageController extends BaseController 
{
    public $data = [
        'page_title' => "{{pageName}} Page"
    ];

    public function getData()
    {
        $this->data['name'] = "{{fakerName}}";

        return $this->respond($this->data);
    }
}
