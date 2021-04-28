<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';
require_once __dir__.'/../models/Questionnaire.php';
require_once __dir__.'/../models/Answer.php';

class Question extends Model
{
    const TABLE = 'questions';
    public function __construct()
    {
        $this->content = CharField::init('content', ['max' => 256]);
        $this->questionnaire_id = ForeignField::init('questionnaire_id', Questionnaire::class);
    }

    public function get_answers()
    {
        return Answer::filter([ ['question_id', '=', $this->id] ]);
    }

    public function delete_answers()
    {
        $answers = $this->get_answers();
        foreach($answers as $answer){
            $answer->delete();
        }
    }
}

?>