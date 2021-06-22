<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';
require_once __dir__.'/../traits/ExternalDatabase.php';
require_once __dir__.'/../models/Status.php';
require_once __dir__.'/../models/HolidayKind.php';

class HolidayEvent extends Model
{
    const TABLE = 'holiday_events';
    use ExternalDatabase;

    public function __construct()
    {
        $this->from_date = DateField::init('from_date');
        $this->to_date = DateField::init('to_date');
        $this->additional_info = TextField::init('additional_info');
        $this->employee_id = IntegerField::init('employee_id');
        $this->status_id = ForeignField::init('status_id', Status::class);
        $this->kind_id = ForeignField::init('kind_id', HolidayKind::class);
    }

    public function get_employee()
    {
        $employees = $this->external_query("SELECT * FROM people WHERE id=".$this->employee_id);
        $this->employee = $employees ? $employees[0] : null;
    }

    public function get_status()
    {
        $res = Status::sql("select * from statuses where id=".$this->status_id);
        foreach($res as $i) $i->id = $i->id;
        return $res ? $res[0] : null;
    }

    public function get_kind()
    {
        $res = HolidayKind::sql("select * from holiday_kinds where id=".$this->kind_id);
        foreach($res as $i) $i->id = $i->id;
        return $res ? $res[0] : null;
    }
}

?>