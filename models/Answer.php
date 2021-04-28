<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';
require_once __dir__.'/../models/Question.php';

class Answer extends Model
{
    const TABLE = 'answers';
    public function __construct()
    {
        $this->content = TextField::init('content');
        $this->question_id = ForeignField::init('question_id', Question::class);
        $this->created_at = DateTimeField::init('created_at');
    }
}

?>