<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request)
    {
        // Do something here

    	$session = session();
		$uri = service('uri');
        
		if (!$session->get("login_data") and ($uri->getSegment(1)) != "access" and ($uri->getSegment(1)) != "main" and ($uri->getSegment(1)) != ""){
			return redirect()->to( base_url('access/login/',"refresh") );
		}
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response)
    {
        // Do something here
    }
}