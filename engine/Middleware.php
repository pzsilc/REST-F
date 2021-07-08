<?php
namespace engine\Middleware;

abstract class Middleware
{
    public function reject()
    {
        http_response_code(403);
        echo json_encode([
            'type' => 'error',
            'data' => 'This route is forbidden'
        ]);
        exit();
    }

    public abstract function check();
}

?>