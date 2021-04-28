<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';

class Admin extends Model
{
    const TABLE = 'admins';
    public function __construct()
    {
        $this->person_id = IntegerField::init('person_id');
    }
}

?>