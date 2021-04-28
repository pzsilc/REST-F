<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Admin.php';

class AuthView extends View
{
    const EXTERNAL_DATABASE = 'ppp';


    private function get_user_from_external_db($query)
    {
        global $database;
        $conn = new mysqli($database['host'], $database['user'], $database['password'], self::EXTERNAL_DATABASE);
        $res = $conn->query($query);
        return $res->num_rows === 1 ? (object)$res->fetch_assoc() : null;
    }


    public function login()
    {
        if($this->request->method == 'POST')
        {
            $token = $this->request->post('token');
            $user = $this->get_user_from_external_db("SELECT * FROM people WHERE token='$token'");
            if($user){
                $admin = Admin::filter([ ['person_id', '=', $user->id] ]);
                $this->request->set_session('user', (object)[
                    'is_admin' => boolval($admin)
                ]);
                $target_id = $this->request->get('target_id');
                if(!$target_id)
                    return $this->redirect('/');
                else
                    return $this->redirect('/questionnaires/single?id='.$target_id);
            }
            else $this->add_message('error', 'Niepoprawny token');
        }

        return $this->render('auth.login');
    }

    
    public function logout()
    {
        $this->request->unset_session('user');
        if($this->request->get('with_message'))
            $this->add_message('success', 'Wysłałeś odpowiedzi');
        return $this->redirect('/auth/login');
    }
}

?>