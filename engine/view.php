<?php
require_once __dir__.'/request.php';
require_once __dir__.'/../vendor/autoload.php';
use eftec\bladeone\BladeOne;

class View
{
    protected $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function add_message($type, $text)
    {
        if(!isset($_SESSION['messages'])){ $_SESSION['messages'] = []; }
        array_push($_SESSION['messages'], ['type' => $type, 'text' => $text]);
    }

    private function generate_csrf()
    {
        $token = md5(uniqid());
        $this->request->set_session('csrf_token', $token);
        return "<input type='hidden' name='csrf_token' value='".$this->request->session('csrf_token')."'/>";
    }

    protected function json($data, $code=200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function redirect($url)
    {
        global $app_path;
        header("Location: $app_path$url");
        exit();
    }

    public function render($dir, $args=[])
    {
        $csrf = $this->generate_csrf();
        global $app_name;
        global $app_path;
        $messages = [];
        if(isset($_SESSION['messages'])){
            $messages = $_SESSION['messages'];
            unset($_SESSION['messages']);
        }

        $views = 'statics/templates';
        $cache = 'engine/cache';
        $user = $this->request->session('holidays_auth');
        $blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        echo $blade->run($dir, array_merge([
                'app_name' => $app_name, 
                'app_path' => $app_path,
                'csrf' => $csrf,
                'messages' => $messages,
                'user' => $user
            ], 
            $args
        ));
    }
}

?>