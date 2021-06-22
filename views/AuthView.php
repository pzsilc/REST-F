<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Admin.php';
require_once __dir__.'/../traits/ExternalDatabase.php';

class AuthView extends View
{
    use ExternalDatabase;

    public function login()
    {
        $data = $this->request->post;
        $email = $data['email'];
        $token = $data['token'];
        $res = $this->external_query("SELECT * FROM people WHERE email='$email' AND token='$token'");
        if(count($res) === 1)
        {
            $user = end($res);
            $res = Admin::filter([ ['employee_id', '=', $user->id] ]);
            $is_admin = boolval($res);
            return $this->json([
                'type' => 'success',
                'data' => array_merge((array)$user, ['is_admin' => $is_admin])
            ]); 
        }
        else
        {
            return $this->json([
                'type' => 'error',
                'data' => 'Coś poszło nie tak, spróbuj jeszcze raz'
            ], 404);
        }
    }
}

?>