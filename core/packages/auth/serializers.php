<?php

namespace core\packages\auth\serializers;
use core\Serializer\Serializer;
use core\Fields;
use core\packages\auth\models;



class TokenSerializer extends Serializer
{
    const MODEL = models\Token::class;
    const FIELDS = ['value', 'user'];
}



class UserSerializer extends Serializer
{
    const MODEL = models\User::class;
    const FIELDS = ['id', 'email', 'is_admin'];
}


?>