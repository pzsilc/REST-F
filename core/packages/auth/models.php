<?php

namespace core\packages\auth\models;
use core\Model\Model;
use core\Fields;
require_once 'traits.php';



class User extends Model
{
    public function __construct()
    {
        $this->email = Fields\EmailField::init(['max_length' => 64]);
        $this->password = Fields\PasswordField::init(['max_length' => 256]);
        $this->is_admin = Fields\BooleanField::init();
    }

    public function __toString()
    {
        return $this->email;
    }
}


class Token extends Model{
    use \engine\packages\auth\traits\IsGenerating;

    public function __construct()
    {
        $this->value = Fields\CharField::init(['max_length' => 128]);
        $this->expire_time = Fields\DateTimeField::init();
        $this->user = Fields\ForeignField::init(User::class);
    }

    public function __toString()
    {
        return $this->value;
    }
}


?>