<?php
namespace core\traits\HasTableName;

trait HasTableName
{
    //returns table name basic on class name
    public static function table()
    {
        $class = get_called_class();
        $splited_class_name = explode('\\', $class);
        $tablename = $splited_class_name[count($splited_class_name) - 1];
        $tablename = lcfirst($tablename);
        
        //exceptions
        $res = '';
        switch($tablename)
        {
            case 'person': $res = 'people'; break;
            default: {
                if(substr($tablename, -1) === 'y')
                {
                    $res = substr($tablename, 0, strlen($tablename) - 1).'ies';
                }
                else
                {
                    $res = $tablename.'s';
                }
            }
        }

        $res = preg_split('/(?=[A-Z])/', $res);
        return strtolower(implode('_', $res));
    }
}

?>