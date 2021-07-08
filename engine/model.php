<?php
namespace engine\Model;
require_once __dir__.'/../engine/traits/Queriable.php';
require_once __dir__.'/../engine/traits/HasTableName.php';

abstract class Model
{
    const DB = null;
    public $id = null;
    use \engine\traits\Queriable\Queriable;
    use \engine\traits\HasTableName\HasTableName;

    public function __toString()
    {
        return strval($this->id);
    }
}

?>