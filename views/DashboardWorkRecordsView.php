<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Admin.php';
require_once __dir__.'/../traits/ExternalDatabase.php';


class DashboardWorkRecordsView extends View
{
    use ExternalDatabase;

    public function __construct()
    {
        parent::__construct();
        $user_id = $this->request->post('user_id');
        $res = Admin::filter([ ['employee_id', '=', $user_id] ]);
        if(!$res){
            $this->json([
                'type' => 'error',
                'data' => 'Nie jesteś upoważniony do wykonywania tej operacji'
            ], 401);
            exit();
        }
    }

    public function get()
    {
        
    }
}

?>