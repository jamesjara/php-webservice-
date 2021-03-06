<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Free PHP CRUD RESTful Web Service
 *
 * Save time by creating easy and reusable CRUD RESTful web service.
 *
 * PHP version 5
 *
 * LICENSE: http://opensource.org/licenses/MIT
 *
 * @category Php REST CRUD webservice
 * @author James Jara <jamesjara@gmail.com>
 * @copyright 2009-2015 The PHP Group
 * @license http://opensource.org/licenses/MIT
 * @version GIT: 3
 * @link http://pear.php.net/package/x7cloud
 */
namespace JamesJara\X7Cloud;

/**
 * Abstract Class to create own webservice class, you have to declare endpoint
 * calls as internal methods, any method MUST start with a prefix.
 *
 * Please see README to configure with .htaccess to map endpoint as pretty urls
 *
 * @abstract
 *
 * @category Php CRUD webservice
 * @author James Jara <jamesjara@gmail.com>
 */
abstract class X7Cloud
{

    const VERSION = '3.0';

    const DATE_APPROVED = '2014-11-09';

    /**
     * Prefix to lookup functions inside the code, with this we can avoid users
     * calling internal functions
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * The service requested by the URI endpoint
     *
     * @var string
     */
    protected $module = '';

    /**
     * The second params requested by the URI endpoint
     *
     * @var string
     */
    protected $verb = '';

    /**
     * The HTTP method: GET, POST, PUT or DELETE
     *
     * @var string
     */
    protected $method = '';

    /**
     * Extra URI parms, after module and verb /<module>/<verb>/<args>/<args>
     *
     * @var array
     */
    protected $args = Array();

    /**
     * Stream of the PUT request
     */
    protected $payload = null;

    /**
     * Returns the current endpoint module
     *
     * @param boolean $isIntern
     * @return String if true return the internal string value, else the
     */
    private function getModule($isIntern)
    {
        return ($isIntern == true) ? $this->prefix . $this->module : $this->module;
    }

    /**
     * Set current module based on REQUEST
     *
     * @param string $endpoint
     */
    private function setModule($endpoint)
    {
        $this->module = $endpoint;
    }

    /**
     * Create a new X7Cloud instance.
     *
     * @return void
     */
    public function __construct()
    {
        $request = $_REQUEST['request'];

        $this->args = explode('/', rtrim($request, '/'));
        $this->setModule(array_shift($this->args));
        if (array_key_exists(0, $this->args) && ! is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else
                if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                    $this->method = 'PUT';
                } else {
                    throw new Exception("Unexpected Header");
                }
        }

        switch ($this->method) {
            case 'DELETE':
                $this->request = $_POST;
                break;
            case 'POST':
                $this->request = $_POST;
                $this->payload = file_get_contents("php://input");
                break;
            case 'GET':
                $this->request = $_GET;
                break;
            case 'PUT':
                $this->request = $_GET;
                $this->payload = file_get_contents("php://input");
                break;
            default:
                $this->error(405);
                break;
        }
    }

    /**
     * lookup for the requested method(module).
     *
     * @return mixed
     */
    public function processAPI()
    {
        if (method_exists($this, $this->getModule(true)) === True) {
            $this->execute($this->{$this->getModule(true)}($this->args));
        }
        return $this->error(404);
    }

    /**
     * Abort all php process and return HTTP status code to the connected client
     *
     * @param string $data
     * @param string $code
     * @return mixed
     */
    private function error($code = 200)
    {
        $status = array(
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error'
        );
        $status = ($status[$code]) ? $status[$code] : $status[500];
        header("HTTP/1.1 " . $code . " " . $status);
        throw new Exception("Unexpected issue " . $status);
    }

    /**
     * Dynamically execute methods
     *
     * @param Response\IResponse $responseType
     * @return mixed
     */
    public function execute(Response\IResponse $responseType)
    {
        echo $responseType->response();
        exit();
    }
}

