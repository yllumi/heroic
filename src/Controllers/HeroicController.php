<?php namespace Yllumi\Heroic\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class HeroicController extends Controller
{
    use ResponseTrait;

	/**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

	public $data = [
		'page_title' => 'Page Title'
	];

	protected $pageTemplate;

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
    }

	// Render shell template
	public function getIndex()
	{
		return pageView('layout', $this->data);
	}

	// Render inner template
	public function getTemplate()
    {
		// Set $pageTemplate automatically based on folder path
		$classPathDir = dirname((new \ReflectionClass(static::class))->getFileName());
		$this->pageTemplate = str_replace(APPPATH .'Pages/', '', $classPathDir) . '/template';
		
        return pageView(trim($this->pageTemplate,'/'), $this->data);
    }

    protected function respondSecure($data = null, int $status = 200, string $message = '')
    {
        // ✅ KEAMANAN: Hanya izinkan same-origin (di production)
        if (ENVIRONMENT === 'production') {
            $allowedHost = $_SERVER['HTTP_HOST'] ?? '';
            $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';

            if ($origin && parse_url($origin, PHP_URL_HOST) !== $allowedHost) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 0, 'message' => 'Forbidden: Invalid origin']);
            }

            // Validasi AJAX header
            $requestedWith = $this->request->getHeaderLine('X-Requested-With');
            if (strtolower($requestedWith) !== 'xmlhttprequest') {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 0, 'message' => 'Forbidden: Only AJAX allowed']);
            }
        }

        // 🟢 Lanjutkan normal kalau valid
        return $this->respond($data, $status, $message);
    }

}
