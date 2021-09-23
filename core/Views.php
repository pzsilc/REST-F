<?php
namespace core\Views;
namespace core\Request;
require_once __dir__.'/../core/traits/CRUD.php';

abstract class View
{
    const ACTIONS_HTTP_METHOD_ARRAY = [
        '_list' => 'GET',
        'retrieve' => 'GET',
        'create' => 'POST',
        'update' => 'PATCH',
        'destroy' => 'DELETE'
    ];

    const PERMISSIONS = [];
    const SERIALIZER = null;
    const MODEL = null;

    public $request = null;

    public function __construct()
    {
        $this->request = new Request();
    }

    public static function __callStatic($function, $params)
    {
        $class = get_called_class();
        $denied_methods = ['get_serializer', 'get_model', 'get_queryset', 'get_object', 'get_permissions'];
        if(in_array($function, $denied_methods))
        {
            throw new \Exception("Method $function is denied. This routes are denied: ".implode(', ', $denied_methods));
        }
        if($function === 'as_view')
        {
            return $class::as_view();
        }
        $function = substr($function, 1);
        $splited_class_name = explode('\\', $class);
        return [$splited_class_name[count($splited_class_name) - 1], $function];
    }

    private static function as_view()
    {
        $res = [];
        foreach(self::ACTIONS_HTTP_METHOD_ARRAY as $action => $method)
        {
            if(method_exists(get_called_class(), $action))
            {
                $postfix = in_array($action, ['retrieve', 'update', 'destroy']) ? '<:id>/' : '';
                $res[] = [get_called_class(), $postfix, $action, $method];
            }
        }
        return $res;
    }

    public function get_serializer($instance=null, $data=[], $many=false)
    {
        $class = get_called_class();
        $serializer = $class::SERIALIZER;
        return $serializer ? new $serializer($instance, $data, $many) : null;
    }

    public function get_model()
    {
        $class = get_called_class();
        $model = $class::MODEL;
        return $model ? new $model() : null;
    }

    public function get_queryset()
    {
        $class = get_called_class();
        $model = $class::MODEL;
        return $model ? $model::all() : [];
    }

    public function get_object($id)
    {
        $class = get_called_class();
        $model = $class::MODEL;
        return $model ? $model::get_object_or_404($id) : null;
    }

    public function get_permissions()
    {
        $class = get_called_class();
        return $class::PERMISSIONS;
    }
}



/*

Default view classes for custom,
each of them has own available actions.

If you want to user your own view, you can inherite
by APIView that actually is pure Views\View.

*/

abstract class ListView extends View
{
    use \core\traits\CRUD\HasList;
}

abstract class RetrieveView extends View
{
    use \core\traits\CRUD\HasRetrieve;
}

abstract class CreateView extends View
{
    use \core\traits\CRUD\HasCreate;
}

abstract class UpdateView extends View
{
    use \core\traits\CRUD\HasUpdate;
}

abstract class DestroyView extends View
{
    use \core\traits\CRUD\HasDestroy;
}

abstract class ListCreateView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasCreate;
}

abstract class ListRetrieveView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasRetrieve;
}

abstract class ListUpdateView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasUpdate;
}

abstract class ListDestroyView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasDestroy;
}

abstract class CreateRetrieveView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasRetrieve;
}

abstract class CreateUpdateView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasUpdate;
}

abstract class CreateDestroyView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasDestroy;
}

abstract class RetrieveUpdateView extends View
{
    use \core\traits\CRUD\HasRetrieve;
    use \core\traits\CRUD\HasUpdate;
}

abstract class RetrieveDestroyView extends View
{
    use \core\traits\CRUD\HasRetrieve;
    use \core\traits\CRUD\HasDestroy;
}

abstract class UpdateDestroyView extends View
{
    use \core\traits\CRUD\HasUpdate;
    use \core\traits\CRUD\HasDestroy;
}

abstract class ListCreateRetrieveView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasRetrieve;
}

abstract class ListCreateUpdateView extends View
{
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasUpdate;
}

abstract class RetrieveUpdateDestroyView extends View
{
    use \core\traits\CRUD\HasRetrieve;
    use \core\traits\CRUD\HasUpdate;
    use \core\traits\CRUD\HasDestroy;
}

abstract class CreateListDestroyView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasDestroy;
}

abstract class CreateListRetrieveDestroyView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasRetrieve;
    use \core\traits\CRUD\HasDestroy;
}

abstract class ModelView extends View
{
    use \core\traits\CRUD\HasCreate;
    use \core\traits\CRUD\HasList;
    use \core\traits\CRUD\HasRetrieve;
    use \core\traits\CRUD\HasUpdate;
    use \core\traits\CRUD\HasDestroy;
}

abstract class APIView extends View {}

?>
