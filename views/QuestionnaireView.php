<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Questionnaire.php';
require_once __dir__.'/../models/Answer.php';

class QuestionnaireView extends View
{

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->request->session('user');
        $this->questionnaire_id = $this->request->get('id');
        if(!$this->user){
            $target = '/auth/login';
            if($this->questionnaire_id) $target .= '?target_id='.$this->questionnaire_id;
            return $this->redirect($target);
        }
    }


    public function index()
    {
        $questionnaires = Questionnaire::all();
        return $this->render('questionnaires.index', [
            'questionnaires' => $questionnaires
        ]);
    }


    public function single()
    {
        $questionnaire = Questionnaire::get_object_or_404($this->questionnaire_id);
        return $this->render('questionnaires.single', [
            'questionnaire' => $questionnaire,
            'questions' => $questionnaire->get_questions(),
            'title' => $questionnaire->title
        ]);
    }


    public function post()
    {
        $data = $this->request->post;
        unset($data['csrf_token']);
        foreach($data as $key => $val){
            $q_id = explode('_', $key)[1];
            $answer = new Answer();
            $answer->content = $val;
            $answer->question_id = $q_id;
            $answer->created_at = date('Y-m-d H:i:s');
            $answer->save();
        }

        return $this->redirect('/auth/logout?with_message=true');
    }

}

?>