<?php
namespace core\Model;
require_once __dir__.'/../core/traits/Queriable.php';
require_once __dir__.'/../core/traits/HasTableName.php';

abstract class Model
{
    const DB = null;
    const READ_ONLY = false;

    public $id = null;
    use \core\traits\Queriable\Queriable;
    use \core\traits\HasTableName\HasTableName;

    public function __toString()
    {
        return strval($this->id);
    }
}

?>
