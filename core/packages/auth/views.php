<?php

namespace core\packages\auth\views;
use core\Views;
use core\Response\Response;
use core\packages\auth\models;
use core\packages\auth\serializers;
use core\packages\auth\permissions;





class UserAPIView extends Views\APIView
{
    const SERIALIZER = serializer\UserSerializer::class;
    const PERMISSIONS = [permissions\IsAuthenticated::class];

    public function get_user($request)
    {
        $serializer = $this->get_serializer($request->user);
        return new Response([
            'type' => 'success',
            'data' => $serializer->data
        ], 200);
    }
}





class LoginAPIView extends Views\APIView
{
    const PERMISSIONS = [permissions\GuestOnly::class];

    public function login($request)
    {
        $email = $request->post('email');
        $password = $request->post('password');
        $password = hash('sha256', $password);
        if($user = models\User::get(
            ['email', '=', $email], 
            ['password', '=', $password]
        ))
        {
            $user_tokens = models\Token::filter(['user', '=', $user->id])->exe();
            foreach($user_tokens as $token)
            {
                $token->delete();
            }
            $token = models\Token::generate($user);
            return new Response([
                'type' => 'success',
                'data' => $token['value']
            ]);
        }
        else return new Response([
            'type' => 'error',
            'data' => 'Dane są niepoprawne'
        ], 400);
    }
}





class LogoutAPIView extends Views\APIView
{
    const PERMISSIONS = [permissions\IsAuthenticated::class];

    public function logout($request)
    {
        $user_tokens = models\Token::filter(['user', '=', $request->user])->exe();
        foreach($user_tokens as $token)
        {
            $token->delete();
        }
        return new Response([
            'type' => 'success',
            'data' => 'Zostałeś wylogowany pomyślnie'
        ], 200);
    }
}



?>