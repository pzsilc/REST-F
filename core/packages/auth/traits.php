<?php

namespace core\packages\auth\traits;
use core\packages\auth\models;
use core\packages\auth\serializers;


trait IsGenerating
{
    public static function generate($user)
    {
        $res = "";
        $length = 128;
        while(true)
        {
            $res = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
            if(!models\Token::get(['value', '=', $res]))
                break;
        }
        $serializer = new serializers\TokenSerializer(null, [
            'value' => $res, 
            'person' => $user->id,
            'expire_time' => date('Y-m-d h:i:s', strtotime('+2 weeks'))
        ]);
        $serializer->is_valid();
        $serializer->save();
        return $serializer->data;
    }
}

?>