<?php
require_once __dir__.'/../engine/view.php';
require_once __dir__.'/../models/Notification.php';

class NotificationView extends View
{
    public function get()
    {
        $user_id = $this->request->post('user_id');
        $notifications = Notification::filter([ ['user_id', '=', $user_id] ]);
        foreach($notifications as $n) $n->id = $n->id;
        return $this->json([
            'type' => 'success',
            'data' => $notifications
        ], 200);
    }

    public function put()
    {
        $id = $this->request->post('notification_id');
        $user_id = $this->request->post('user_id');
        $notification = Notification::get_object_or_404($id);
        if($notification->user_id != $user_id)
            return $this->json([ 
                'type' => 'error',
                'data' => 'Nie jesteś upoważniony do wykonania tej akcji'
            ], 401);
        $notification->readed = true;
        $notification->save();
        return $this->json([
            'type' => 'success',
            'data' => 'Przyczytano powiadomienie'
        ], 204);
    }
}

?>