<?php
namespace App\Core;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

class BaseController extends Controller
{

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['url', 'form', 'infector', 'session'];

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        // $this->session = \Config\Services::session();

        $session = session();
        $uri = service('uri');
        $uriString = $this->request->uri;

    }

    public function hash($password)
    {

        $salt = rand(111111, 999999);
        $password = hash("sha512", $salt . $password);

        $hash = array(
            "salt" => $salt,
            "password" => $password,
        );

        return $hash;
    }

    public function debug($data)
    {

        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        die();

    }

    public function checkExists($username, $exclude_id = "")
    {

        $where = array(
            "username" => $username,
        );

        if ($exclude_id == "") {

            $admin = $this->AdminModel->getWhere($where);
            $user = $this->UserModel->getWhere($where);

            if (empty($admin) and empty($user)) {

                return false;

            } else {
                return true;
            }

        } else if ($exclude_id != "") {
            $admin = $this->AdminModel->getWhereAndPrimaryIsNot($where, $exclude_id);
            $user = $this->UserModel->getWhereAndPrimaryIsNot($where, $exclude_id);

            if (empty($admin) and empty($user)) {

                return false;

            } else {

                return true;
            }
        }

    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function show404(){
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    function empty404($data){
        if(empty($data)){
            $this->show404();
        }
    }

    public function conn($url)
    {
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',

        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }
}
