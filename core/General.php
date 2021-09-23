<?php
namespace core\General;

class General
{
    //load settings file
    public static function get_settings()
    {
        $content = file_get_contents(__dir__.'/../server/settings.json');
        return (array)json_decode($content);
    }

    //load packages
    public static function load_packages($apps)
    {
        require_once __dir__.'/../core/Settings.php';
        require_once __dir__.'/../core/Fields.php';
        require_once __dir__.'/../core/Model.php';
        require_once __dir__.'/../core/Query.php';
        require_once __dir__.'/../core/Response.php';
        require_once __dir__.'/../core/Request.php';
        require_once __dir__.'/../core/Serializer.php';
        require_once __dir__.'/../core/Permission.php';
        require_once __dir__.'/../core/URL.php';
        require_once __dir__.'/../core/Views.php';
        //built in traits
        require_once __dir__.'/../core/traits/CRUD.php';
        require_once __dir__.'/../core/traits/HasTableName.php';
        require_once __dir__.'/../core/traits/Queriable.php';
        //custom models, views, serializers, traits
        foreach($apps as $app)
        {
            require_once __dir__."/../$app/models.php";
            require_once __dir__."/../$app/serializers.php";
            require_once __dir__."/../$app/views.php";
            require_once __dir__."/../$app/permissions.php";
            require_once __dir__."/../$app/traits.php";
        }
    }

    //CORS config
    public static function cors($settings)
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PATCH");

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

        $matched = false;

        foreach($urls as $url)
        {
            $res = \engine\URL\URL::get_regex_and_pattern_urls_from_url($url);
            $regex = $res[0];
            $pattern = $res[1];
            $request = new \engine\Request\Request;
            if (preg_match_all('/^'.$regex.'$/', $current_url) && strtolower($request->method) === strtolower($url[3]))
            {
                $vars = \engine\URL\URL::get_vars_from_url($pattern, $current_url);
                $matched = true;
                $a = explode('\\', $url[1]);
                $view = $a[count($a) - 1];
                $method = $url[2];
                require_once "app/views.php";
                eval('$v = @new \\app\\views\\'.$view.'();');
                eval('try{ $permissions = \\app\\views\\'.$view.'::PERMISSIONS; }catch(\\Error $e){ $permissions=[]; }');
                if($permissions)
                {
                    foreach($permissions as $permission)
                    {
                        eval('$permission = new \\'.$permission.'();');
                        if(!$permission->check($request, $view, $method))
                            $permission->deny();
                    }
                }
                eval('$v->$method($request'.($vars ? ', ' : '').implode(', ', $vars).');');
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
