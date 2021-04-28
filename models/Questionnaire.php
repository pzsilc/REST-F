<?php

require_once __dir__.'/../engine/model.php';
require_once __dir__.'/../engine/fields.php';
require_once __dir__.'/../models/Question.php';

class Questionnaire extends Model
{
    const TABLE = 'questionnaires';
    public function __construct()
    {
        $this->title = CharField::init('title', ['max' => 64]);
        $this->created_at = DateTimeField::init('created_at');
    }

    public function get_questions()
    {
        return Question::filter([ ['questionnaire_id', '=', $this->id] ]);
    }

    public function delete_questions()
    {
        $questions = $this->get_questions();
        foreach($questions as $question){
            $question->delete_answers();
            $question->delete();
        }
    }
}

?>