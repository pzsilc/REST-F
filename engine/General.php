<?php
namespace engine\General;

class General
{
    //load settings file
    public static function get_settings()
    {
        $content = file_get_contents(__dir__.'/../app/settings.json');
        return (array)json_decode($content);
    }

    //load packages
    public static function load_packages()
    {
        require_once __dir__.'/../engine/Fields.php';
        require_once __dir__.'/../engine/Model.php';
        require_once __dir__.'/../engine/Query.php';
        require_once __dir__.'/../engine/Request.php';
        require_once __dir__.'/../engine/Serializer.php';
        require_once __dir__.'/../engine/Middleware.php';
        require_once __dir__.'/../engine/URL.php';
        require_once __dir__.'/../engine/View.php';
        //built in traits
        require_once __dir__.'/../engine/traits/CRUDable.php';
        require_once __dir__.'/../engine/traits/HasTableName.php';
        require_once __dir__.'/../engine/traits/Queriable.php';
        //custom models, views, serializers, traits
        foreach(['models', 'views', 'serializers', 'app/middlewares'] as $resource)
        {
            $files = scandir(__dir__.'/../'.$resource);
            array_shift($files);
            array_shift($files);
            foreach($files as $file)
            {
                if($file === '.gitignore')
                {
                    continue;
                }
                require_once __dir__.'/../'.$resource.'/'.$file;
            }
        }
    }

    //CORS config
    public static function cors($settings)
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }
    }

    //extract url
    public static function extract_url()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url)[0];
        $first_slash_pos = stripos($url, '/', 1);
        return substr($url, $first_slash_pos);
    }

    //check if current_url matchs with any url from available_urls and run this defined function in that url
    public static function match_and_run($current_url, $available_urls)
    {
        $urls = [];
        foreach($available_urls as $au)
        {
            foreach($au as $u)
            {
                $urls[] = $u;
            }
        }

        $request_method = $_SERVER['REQUEST_METHOD'];
        $matched = false;
        foreach($urls as $url)
        {
            if (trim('/api'.$url[0]) === trim($current_url) && strtolower($request_method) === strtolower($url[3]))
            {
                $matched = true;
                $view = $url[1];
                $method = $url[2];
                $middleware = $url[4];
                if($middleware)
                {
                    $middleware = explode('\\', $middleware)[2];
                    require_once __dir__.'/../app/middlewares/'.$middleware.'.php';
                    eval('$middleware = new \\middlewares\\'.$middleware.'\\'.$middleware.'();');
                    if(!$middleware->check())
                    {
                        $middleware->reject();
                    }
                }
                require_once "engine/handlers.php";
                require_once "views/$view.php";
                eval('$v = @new \\views\\'.$view.'\\'.$view.'('.$method.');');
                $v->$method(new \engine\Request\Request);
            }
        }        
        //if no matches
        if(!$matched)
        {
            http_response_code(404);
            echo json_encode([
                'type' => 'error',
                'data' => 'URL not found'
            ]);
        }
    }
}

?>