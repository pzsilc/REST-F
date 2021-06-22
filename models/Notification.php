<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';

class Notification extends Model
{
    const TABLE = 'notifications';
    public function __construct()
    {
        $this->content = TextField::init('content');
        $this->user_id = IntegerField::init('user_id');
        $this->created_at = DateTimeField::init('created_at');
        $this->readed = BooleanField::init('readed');
    }

    public static function create($text, $user_id)
    {
        $notification = new Notification();
        $notification->content = $text;
        $notification->user_id = $user_id;
        $notification->created_at = date('Y-m-d h:i:s');
        $notification->readed = false;
        $notification->save();
        return $notification;
    }
}

?>