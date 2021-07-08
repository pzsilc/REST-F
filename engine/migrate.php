<?php
require_once 'General.php';
use engine\General\General;
General::load_packages();


final class Migrator
{
    private $model = '';
    private $table = null;
    private $attrs = [];
    private $db_columns = [];
    public static $db_all_tables = [];
    public static $migrated_tables = [];

    public static function get_tables($conn)
    {
        $res = $conn->query("SHOW FULL TABLES");
        while($row = $res->fetch_assoc()) 
        {
            $r = null;
            foreach($row as $v)
            { 
                $r = $v; break; 
            }
            array_push(Migrator::$db_all_tables, $r);
        }
    }

    public static function delete_those_exist($databases)
    {
        $groups = [];
        foreach(Migrator::$migrated_tables as $tab)
            $groups[$tab[1]][] = $tab;

        foreach($groups as $key => $val)
        {
            $db = null;
            foreach($databases as $d)
            {
                if($d->db === $key)
                {
                    $db = $d;
                }
            }

            if(!$db)
            {
                break;
            }

            $db = (array)$db;
            $conn = new mysqli($db['host'], $db['user'], $db['password'], $db['db']);
            $res = $conn->query("SHOW FULL TABLES");
            $tables = [];
            while($row = $res->fetch_assoc()) $tables[] = $row;
            foreach($tables as $table)
            {
                if(!in_array($table['Tables_in_'.$key], array_map(function($elem){
                    return $elem[0];
                }, $val)))
                {
                    $conn->query("DROP TABLE ".$table['Tables_in_'.$key]);
                    echo "\e[94mDeleted table ".$table['Tables_in_'.$key]."\e[39m\r\n";
                }
            }
        }
        
    }

    private static function get_full_setting_for_column($attr)
    {
        $prop = $attr->name.' '.$attr->to_sql();
        if(!isset($attr->settings['required']) || $attr->settings['required'] == true) 
        {
            $prop .= ' NOT NULL';
        }
        if(isset($attr->settings['unique']) && $attr->settings['unique'] == true) 
        {
            $prop .= ' UNIQUE';
        }
        if(isset($attr->settings['default'])) 
        {
            $prop .= " DEFAULT '".$attr->settings['default']."'";
        }
        return $prop;
    }

    public function __construct($md)
    {
        $model = explode('.', $md)[0];
        require_once __dir__."/../models/$model.php";
        $this->model = $model;
        eval('$this->table = \\models\\'.$model.'\\'.$model.'::table();');
        eval('$this->attrs = get_object_vars(new \\models\\'.$model.'\\'.$model.'());');
        unset($this->attrs['id']);
        if(!$this->attrs)
        {
            throw new Exception("\e[31mModel $model has no attributes.\e[39m");
        }
    }

    public function run($conn)
    {
        if(false !== $key = array_search($this->table, Migrator::$db_all_tables))
        {
            //update table
            $res = $conn->query("DESCRIBE $this->table");
            while($row = $res->fetch_assoc())
            {
                array_push($this->db_columns, $row);
            }
            unset(Migrator::$db_all_tables[$key]);

            $props = [];
            foreach($this->attrs as $attr)
            {
                $match = false;
                $_s = '';
                $fs = self::get_full_setting_for_column($attr);
                foreach($this->db_columns as $key => $col)
                {
                    if($col['Field'] === $attr->name)
                    {
                        $_s = 'MODIFY COLUMN '.$fs;
                        $match = true;
                        unset($this->db_columns[$key]);
                        array_push($props, $_s);
                    }
                }
                if(!$match)
                {
                    $_s = 'ADD COLUMN '.$fs;
                    array_push($props, $_s);
                }
            }
            foreach($this->db_columns as $col)
            {
                if($col['Field'] === 'id') 
                {
                    continue;
                }
                $_s = 'DROP COLUMN '.$col['Field'];
                array_push($props, $_s);
            }

            $sql = "ALTER TABLE $this->table ".implode(', ', $props);
            $conn->query($sql);
            echo "\e[36mUpdated $this->table table\e[39m\r\n";
        }
        else 
        {
            //create new table
            $props = ['id INT NOT NULL PRIMARY KEY AUTO_INCREMENT'];
            foreach($this->attrs as $attr)
            {
                $prop = self::get_full_setting_for_column($attr);
                array_push($props, $prop);
            }

            $sql = "CREATE TABLE $this->table (".implode(', ', $props).")";
            $conn->query($sql);
            echo "\e[92mCreated $this->table table\e[39m\r\n";
        }
    }
}

//get db settings
$settings = (array)json_decode(file_get_contents(__dir__.'/../app/settings.json'));
$databases = (array)$settings['databases'];
//get models
$dir = scandir('../models');
//iter over models
foreach(array_slice($dir, 2) as $file)
{
    if($file === '.gitignore')
    {
        continue;
    }

    require_once __dir__.'/../models/'.$file;
    $model = explode('.', $file)[0];
    eval('$db = \\models\\'.$model.'\\'.$model.'::DB;');
    eval('$tab = \\models\\'.$model.'\\'.$model.'::table();');
    if(!$db) 
    {
        $db = $settings['defaultDatabase'];
    }
    $databases = (array)$settings['databases'];
    foreach($databases as $d)
    {
        if($d->alias === $db)
        {
            $database = $d;
            break;
        }
    }
    if(!$database)
    {
        throw new Exception("$model has wrong database defined");
    }

    $database = (array)$database;

    $conn = new mysqli(
        $database['host'],
        $database['user'],
        $database['password'],
        $database['db']
    );

    Migrator::$migrated_tables[] = [$tab, $db];

    Migrator::get_tables($conn);
    //migrate each next model
    $migrator = new Migrator($file);
    $migrator->run($conn);
    $conn->close();
}

//deleting removed models
Migrator::delete_those_exist($databases);

?>