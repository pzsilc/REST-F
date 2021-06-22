<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../traits/ExternalDatabase.php';
require_once __dir__.'/../models/HolidayEvent.php';
require_once __dir__.'/../models/Notification.php';
require_once __dir__.'/../models/Admin.php';

class HolidaysView extends View
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

    //argumenty: employee_id (id pracownika), all (jeśli true to zwróci wszystkie eventy pracownika. W przeciwnym wypadku zwróci tylko te o statusie 'BRAK')
    public function get()
    {
        $employee_id = $this->request->post('employee_id');
        $res = $this->external_query("SELECT * FROM people WHERE id=$employee_id");
        if(!$res){
            return $this->json([
                'type' => 'error',
                'data' => 'Nie znaleziono pracownika'
            ], 404);
        }
        $employee = end($res);
        $filters = [ ['employee_id', '=', $employee->id] ];
        if($this->request->post('all') === "false"){
            array_push($filters, ['status_id', '=', 1]);
        }
        $events = HolidayEvent::filter($filters);
        foreach($events as $e){
            $e->additional_info = html_entity_decode($e->additional_info);
            $e->id = $e->id;
            $e->status = $e->get_status();
            $e->kind = $e->get_kind();
        }
        return $this->json([
            'type' => 'success',
            'data' => $events,
        ]);
    }

    //argumenty: from (od daty), to (do daty), additionalInfo (dodatkowy kontent dla kierownika (nullable)), user_id (id użytkownika)
    public function post()
    {
        $data = $this->request->post;
        $from = strtotime($data['from']);
        $to = strtotime($data['to']);
        if($to < $from){
            return $this->json([
                'type' => 'error',
                'data' => 'Data startowa musi być mniejsza od daty końcowej'
            ], 400);
        }
        $event = new HolidayEvent();
        $event->from_date = $data['from'];
        $event->to_date = $data['to'];
        $event->additional_info = $data['additionalInfo'];
        $event->employee_id = $data['user_id'];
        $event->kind_id = $data['kindId'];
        $event->status_id = $event->kind_id == 2 ? null : 1;
        if($event->kind_id === '2') $message = 'Dodano chorobowe';
        else $message = 'Dodano prośbę urlopową';
        global $frontend_path;
        
        $event->save();
        if($event->kind_id == 2){
            $emp = end($this->external_query("SELECT * FROM people WHERE id=$event->employee_id"));
            Notification::create("Dodano Twoje chorobowe ($event->from_date -> $event->to_date)", $emp->id);
            Notification::create("Dodano chorobowe dla ".$emp->first_name.' '.$emp->last_name." ($event->from_date -> $event->to_date)", $emp->manager_id);
        }
        else{
            Notification::create("Masz prośbę urlopową od ".$this->user->first_name.' '.$this->user->last_name." ($event->from_date -> $event->to_date) 
                <a href='$frontend_path/employees/requests'>Link</a>", $this->user->manager_id);
        }

        return $this->json([
            'type' => 'success',
            'data' => $message
        ], 201);
    }

    //argumenty: id (id urlopu), type (operacja -> możliwości: accept, reject), user_id (id użytkownika)
    //tę metodę wykonuje tylko kierownik podczas akceptacji/odrzucenia prośby
    public function put()
    {
        $id = $this->request->post('id');
        $type = $this->request->post('type');
        $e = HolidayEvent::get_object_or_404($id);
        $action_string = '';
        if($type === "reject"){
            $e->status_id = 3;
            $action_string = 'odrzucił';
        } else {
            $e->status_id = 2;
            $action_string = 'zaakceptował';
        }
        $e->save();
        global $frontend_path;
        $emp = end($this->external_query("SELECT * FROM people WHERE id=$e->employee_id"));
        Notification::create("Twój przełożony $action_string Twoją prośbę urlopową ($e->from_date -> $e->to_date)", $e->employee_id);
        foreach(Admin::all() as $admin){
            Notification::create("Zarejestrowano nowy urlop.<br/>pracownik: ".$emp->first_name.' '.$emp->last_name.", 
                kierownik: ".$this->user->first_name.' '.$this->user->last_name." ($e->from_date -> $e->to_date) <br/>
                <a href='$frontend_path/dashboard/employees/$emp->id'>Link</a>", $admin->employee_id);
        }
        return $this->json([
            'type' => 'success',
            'data' => 'Prośba urlopowa została '.($e->status_id === 2 ? 'zaakceptowana' : 'odrzucona')
        ]);
    }

    //argumenty: id (id urlopu), user_id (id użytkownika)
    public function delete()
    {
        $id = $this->request->post('id');
        $event = HolidayEvent::get_object_or_404($id);
        if($event->employee_id != $this->user->id){
            return $this->json([
                'type' => 'error',
                'data' => 'Nie jesteś upoważniony do usunięcia tej prośby urlopowej'
            ], 401);
        }
        $event->delete();
        return $this->json([
            'type' => 'success',
            'data' => 'Usunięto urlop pomyślnie'
        ]);
    }
}

?>