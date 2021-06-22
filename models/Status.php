<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';

class Status extends Model
{
    const TABLE = 'statuses';
    public function __construct()
    {
        $this->name = CharField::init('name', ['max' => 32]);
    }
}

?>