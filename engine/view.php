<?php
namespace engine\View;
require_once __dir__.'/../engine/traits/CRUDable.php';

abstract class View
{
    use \engine\traits\CRUDable\CRUDable;
    const ALLOWED_ROUTES = [];
    const SERIALIZER = null;
    const MODEL = null;

    public function __construct($action)
    {
        $class = get_called_class();
        if($class::ALLOWED_ROUTES && !in_array($action, $class::ALLOWED_ROUTES))
        {
            $this->response([
                'type' => 'error',
                'data' => 'Permission denied for this route'
            ], 403);
            exit();
        }
    }

    public static function __callStatic($function, $params)
    {
        $class = get_called_class();
        $function = substr($function, 1);
        $splited_class_name = explode('\\', $class);
        return [$splited_class_name[count($splited_class_name) - 1], $function];
    }

    public function response($data, $status = 200)
    {
        http_response_code($status);
        echo json_encode($data);
    }
}

?>