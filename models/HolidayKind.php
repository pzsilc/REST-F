<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';

class HolidayKind extends Model
{
    const TABLE = 'holiday_kinds';
    public function __construct()
    {
        $this->name = CharField::init('name', ['max' => 64]);
    }
}

?>