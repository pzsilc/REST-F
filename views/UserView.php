<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../traits/ExternalDatabase.php';

class UserView extends View
{
    use ExternalDatabase;

    public function __construct()
    {
        parent::__construct();
        $user_id = $this->request->post('user_id');
        $res = $this->external_query("SELECT * FROM people WHERE id=$user_id");
        if(!count($res)){
            $this->json([
                'type' => 'error',
                'data' => 'Nie znaleziono takiego użytkownika'
            ], 404);
            exit();
        }
        $this->user = end($res);
    }

    public function get_holidays()
    {
        return $this->json(null);
    }

    public function get_employees()
    {
        $res = $this->external_query("SELECT * FROM people WHERE manager_id=".$this->user->id);
        $employees = array_map(function($e){
            return (object)[
                'id' => $e->id,
                'first_name' => $e->first_name,
                'last_name' => $e->last_name
            ];
        }, $res);
        return $this->json([
            'type' => 'success',
            'data' => $employees
        ]);
    }

    public function get_manager()
    {
        $res = $this->external_query("SELECT id, first_name, last_name FROM people WHERE id=".$this->user->manager_id);
        if(!count($res)){
            return $this->json([
                'type' => 'error',
                'data' => 'Nie znaleziono takiego użytkownika w bazie'
            ], 404);
        }
        return $this->json([
            'type' => 'success',
            'data' => end($res)
        ]);
    }
}

?>