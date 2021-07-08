<?php
namespace engine\URL;

class URL
{
    const GET = 'GET';
    const POST = 'POST';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    //handling urls
    public static function add($path, $target, $http_method='GET', $middleware=null)
    {
        if(is_array($target[1]))
        {
            $class = $target[0];
            $urls = $target[1];
            return array_map(function($url) use ($path, $class){   
                return [$path, $class, $url, strtoupper($url), $middleware];
            }, $urls);
        }
        else
        {
            return [ [$path, $target[0], $target[1], $http_method, $middleware] ];
        }
    }
}

?>