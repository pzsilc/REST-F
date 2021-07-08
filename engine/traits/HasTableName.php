<?php
namespace engine\traits\HasTableName;

trait HasTableName
{
    //returns table name basic on class name
    public static function table()
    {
        $class = get_called_class();
        $splited_class_name = explode('\\', $class);
        $class = $splited_class_name[count($splited_class_name) - 1];
        $tablename = strtolower($class);
        
        //exceptions
        switch($tablename)
        {
            case 'person': return 'people'; break;
            default: {
                if(substr($tablename, -1) === 'y')
                {
                    return substr($tablename, strlen($tablename) - 1).'ies';
                }
                else
                {
                    return $tablename.'s';
                }
            }
        }
    }
}

?>