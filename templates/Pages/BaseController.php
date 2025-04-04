<?php namespace App\Pages;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Yllumi\Heroic\Controllers\HeroicController;

class BaseController extends HeroicController 
{
	use ResponseTrait;

	protected $helpers = [
		'Yllumi\Ci4Pages\Helpers\pageview',
		'Yllumi\Heroic\Helpers\heroic',
	];
	
	public $data = [
		'page_title' => 'Homepage'
	];

	public function initController(
		RequestInterface $request, 
		ResponseInterface $response, 
		LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

		// Preload any models, libraries, etc, here.

    }

}
