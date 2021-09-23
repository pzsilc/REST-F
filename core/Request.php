<?php
namespace core\Request;
session_start();

class Request
{
    private $method;
    private $post;
    private $patch;
    private $get;
    private $session;
    private $server;
    private $headers;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->post = array_map(function($e){ return htmlentities($e); }, $_POST);
        $this->get = array_map(function($e){ return htmlentities($e); }, $_GET);
        $this->patch = json_decode(file_get_contents('php://input'));
        $this->session = $_SESSION;
        $this->server = $_SERVER;
        $this->headers = getallheaders();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function get($key = null, $default_val = null)
    {
        if(!$key) { return $this->get; }
        return (isset($this->get[$key]) ? $this->get[$key] : $default_val);
    }

    public function post($key = null, $default_val = null)
    {
        if(!$key) { return $this->post; }
        return (isset($this->post[$key]) ? $this->post[$key] : $default_val);
    }

    public function patch($key = null, $default_val = null)
    {
        if(!$key) { return $this->patch; }
        return (isset($this->patch[$key]) ? $this->patch[$key] : $default_val);
    }

    public function session($key = null, $default_val = null)
    {
        if(!$key) { return $this->session; }
        return (isset($this->session[$key]) ? $this->session[$key] : $default_val);
    }

    public function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
        $this->session[$key] = $value;
    }

    public function unset_session($name)
    {
        if(isset($this->session[$name]))
        {
            unset($_SESSION[$name]);
            unset($this->session[$name]);
        }
    }
}

?>
