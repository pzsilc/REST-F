<?php
namespace core\permissions;

abstract class BasePermission
{
    public function deny()
    {
        http_response_code(403);
        echo json_encode([
            'type' => 'error',
            'data' => 'This route is forbidden'
        ]);
        exit();
    }

    public abstract function check($request, $view, $method);
}

?>
