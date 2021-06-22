<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Admin.php';
require_once __dir__.'/../traits/ExternalDatabase.php';

class DashboardView extends View
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

    public function get_employees()
    {
        $keywords = $this->request->post('keywords', '');
        $employee_id = $this->request->post('employee_id', null);
        $query = ($employee_id && $employee_id != 'null') ? "SELECT * FROM people WHERE id=$employee_id" : "SELECT * FROM people 
            WHERE LOWER(first_name) LIKE LOWER('%$keywords%') OR LOWER(last_name) LIKE LOWER('%$keywords%') OR LOWER(email) LIKE LOWER('%$keywords%')";
        $res = $this->external_query($query);
        if($employee_id && $employee_id != 'null'){
            return $this->json([
                'type' => 'success',
                'data' => $res[0],
                'p'=>$query
            ]);
        }
        else{
            $str = '{ "type": "success", "data": [';
            $comma = '';
            foreach($res as $r)
            {
                $str .= $comma . json_encode($r);
                $comma = ',';
            }
            $str .= ']}';
            echo $str;
        }
    }
}

?>