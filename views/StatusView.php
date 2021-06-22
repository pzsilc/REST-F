<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Status.php';

class StatusView extends View
{
    public function get()
    {
        $statuses = Status::all();
        foreach($statuses as $status){
            $status->id = $status->id;
        }
        return $this->json([
            'type' => 'success',
            'data' => $statuses
        ]);
    }
}

?>