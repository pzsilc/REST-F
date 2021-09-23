<?php

namespace core\packages\auth\permissions;
use core\permissions\BasePermission;
use core\packages\auth\models;



class IsAuthenticated extends BasePermission
{
    public function check($request, $view, $method)
    {
        if(!isset($request->headers['Authorization']))
            return false;
        $token = $request->headers['Authorization'];
        $token = explode(' ', $token)[1];
        $token = models\Token::get(
            ['value', '=', $token], 
            ['expire_time', '>', date('Y-m-d h:i:s')]
        );
        if(!$token)
        {
            return false;
        }
        $request->user = models\User::get_object_or_404($token->user);
        return true;
    }
}



class GuestOnly extends BasePermission
{
    public function check($request, $view, $method)
    {
        $h = $request->headers;
        $auth = 'Authorization';
        return !isset($h[$auth]) || !$h[$auth];
    }
}


?>