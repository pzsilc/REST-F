<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Questionnaire.php';
require_once __dir__.'/../models/Question.php';

class DashboardView extends View
{

    public function __construct()
    {
        parent::__construct();
        $this->user = $this->request->session('user');
        if(!$this->user || !$this->user->is_admin)
            return $this->redirect('/auth/login');
    }


    public function index()
    {
        $questionnaires = Questionnaire::all();
        return $this->render('dashboard.index', [
            'user' => $this->user,
            'questionnaires' => $questionnaires
        ]);
    }


    public function create_questionnaire()
    {
        $title = $this->request->post('title');
        $q = new Questionnaire();
        $q->title = $title;
        $q->created_at = date('Y-m-d H:i:s');
        $q->save();
        $this->add_message('success', 'Dodano ankietę');
        return $this->redirect('/dashboard');
    }


    public function delete_questionnaire()
    {
        $questionnaire_id = $this->request->post('questionnaire_id');
        $questionnaire = Questionnaire::get_object_or_404($questionnaire_id);
        $questionnaire->delete_questions();
        $questionnaire->delete();
        $this->add_message('success', 'Usunięto ankietę');
        return $this->redirect('/dashboard');
    }


    public function edit_questionnaire()
    {
        $questionnaire_id = $this->request->post('questionnaire_id');
        $title = $this->request->post('title');
        $questionnaire = Questionnaire::get_object_or_404($questionnaire_id);
        $questionnaire->title = $title;
        $questionnaire->save();
        $this->add_message('success', 'Zaktualizowano ankietę');
        return $this->redirect('/dashboard');
    }


    public function single_questionnaire()
    {
        $id = $this->request->get('id');
        $questionnaire = Questionnaire::get_object_or_404($id);
        return $this->render('dashboard.single', [
            'questionnaire' => $questionnaire
        ]);
    }


    public function create_question()
    {
        $q_id = $this->request->get('questionnaire_id');
        $content = $this->request->post('content');
        $question = new Question();
        $question->content = $content;
        $question->questionnaire_id = $q_id;
        $question->save();
        $this->add_message('success', 'Dodano pytanie');
        return $this->redirect("/dashboard/questionnaires/single?id=$q_id");
    }

    
    public function delete_question()
    {
        $question_id = $this->request->post('question_id');
        $question = Question::get_object_or_404($question_id);
        $questionnaire_id = $question->questionnaire_id;
        $question->delete_answers();
        $question->delete();
        $this->add_message('success', 'Usunięto pytanie');
        return $this->redirect("/dashboard/questionnaires/single?id=$questionnaire_id");
    }


    public function edit_question()
    {
        $question_id = $this->request->post('question_id');
        $content = $this->request->post('content');
        $question = Question::get_object_or_404($question_id);
        $question->content = $content;
        $question->save();
        $this->add_message('success', 'Zaktualizowano pytanie');
        return $this->redirect("/dashboard/questionnaires/single?id=$question->questionnaire_id");
    }

}

?>