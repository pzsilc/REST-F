<?php
namespace core\URL;

class URL
{
    const GET = 'GET';
    const POST = 'POST';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    //handling urls
    public static function add($path, $target, $http_method='GET')
    {
        if(is_array($target[0]))
        {
            //as_view triggered
            return array_map(function($route) use ($path){
                return [
                    $path.$route[1], 
                    $route[0], 
                    $route[2], 
                    $route[3]
                ];
            }, $target);
        }
        else
        {
            return [[
                $path, 
                $target[0], 
                $target[1], 
                $http_method
            ]];
        }
    }


    public static function _include($prefix, $urls_path)
    {
        $urls_path = str_replace('/', '.', $urls_path);
        require_once $urls_path.'php';
        return array_map($urls, function($url) use($prefix) {
            $url[0] = $prefix.$url[0];
            return $url;
        });
    }


    public static function get_vars_from_url($pattern, $current_url)
    {
        $vars = [];
        $arr = explode('/', $current_url);
        $arr2 = explode('/', $pattern);
        $index = 0;
        $num_of_var = 0;
        foreach($arr2 as $el)
        {
            if($el === '(.*)')
            {
                $vars[] = $arr[$index];
            }
            $index++;
        }
        return $vars;
    }


    public static function get_regex_and_pattern_urls_from_url($url)
    {
        $regex_path = $url[0];
        $pattern_path = $regex_path;
        while($start = strpos($regex_path, '<:'))
        {
            $end = strpos($regex_path, '>');
            $v = "";
            for($i = $start + 2; $i < $end; $i++)
            {
                $v .= $regex_path[$i];
            }
            $regex_path = str_replace("<:$v>", "(.*)", $regex_path);
            $pattern_path = str_replace("<:$v>", "(.*)", $pattern_path);
        }
        $regex = '/backend/api/'.$regex_path;
        $regex = str_replace('/', '\/', $regex);
        $pattern = '/backend/api/'.$pattern_path;
        return [$regex, $pattern];
    }
}

?>