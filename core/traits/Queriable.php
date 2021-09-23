<?php
namespace core\traits\Queriable;
use core\Query\Query;

trait Queriable
{

    public static function all()
    {
        $class = get_called_class();
        $query = new Query("SELECT * FROM ".$class::table(), $class);
        return $query->exe();
    }


    public static function get_list()
    {
        $class = get_called_class();
        return new Query("SELECT * FROM ".$class::table(), $class);
    }


    public static function get_object_or_404($id)
    {
        $class = get_called_class();
        $query = new Query("SELECT * FROM ".$class::table()." WHERE id=$id", $class);
        $result = $query->exe();
        if(!$result){
            http_response_code(404);
            json_encode([
                'type' => 'error',
                'data' => 'Not found'
            ]);
            die();
        }
        $result[0]->id = $result[0]->id;
        return $result[0];
    }



    public static function get(...$arr)
    {
        $_arr = [];
        $class = get_called_class();
        foreach($arr as $elem) {
            if(count($elem) != 3) throw new \Exception('Invalid input '.implode(', ', $elem));
            array_push($_arr, $elem[0]." ".$elem[1]." '".$elem[2]."'");
        }
        $cond = implode(' AND ', $_arr);
        $query = new Query("SELECT * FROM ".$class::table()." WHERE (".$cond.")", $class);
        $result = $query->exe();
        return $result ? $result[0] : null;
    }



    public static function filter(...$arr)
    {
        $class = get_called_class();
        $_arr = [];
        foreach($arr as $elem) {
            if(count($elem) != 3) throw new \Exception('Invalid input '.implode(', ', $elem));
            array_push($_arr, $elem[0]." ".$elem[1]." '".$elem[2]."'");
        }
        $cond = implode(' AND ', $_arr);
        return new Query("SELECT * FROM ".$class::table()." WHERE (".$cond.")", $class);
    }



    public static function sql($query)
    {
        $class = get_called_class();
        $query = new Query($query, $class);
        return $query->exe();
    }



    public static function max($col_name)
    {
        $class = get_called_class();
        $query = new Query("SELECT MAX($col_name) as $col_name FROM ".$class::table(), $class);
        return intval($query->exe()[0]->$col_name);
    }



    public static function min($col_name)
    {
        $class = get_called_class();
        $query = new Query("SELECT MIN($col_name) as $col_name FROM ".$class::table(), $class);
        return $query->exe()[$col_name];
    }



    public function save()
    {
        $class = get_called_class();
        if(!$this->id)
        {
            $query = "INSERT INTO ".$class::table()." (";
            $f = true;
            foreach(get_object_vars($this) as $key=>$val)
            {
                if($key == 'id')
                    continue;
                if($f)
                {
                    $query .= $key;
                    $f = false;
                }
                else $query .= ', '.$key;
            }
            $query .= ') VALUES (';
            $div = '';
            foreach(get_object_vars($this) as $key=>$val)
            {
                if($key == 'id')
                    continue;
                if(!is_null($val))
                {
                    $val = '"' . $val . '"';
                }
                else
                    $val = 'NULL';
                $query .= $div . $val;
                $div = ', ';
            }
            $query .= ')';
            $query = new Query($query, $class);
            return $this->id = $query->exe();
        }
        else
        {
            $query = "UPDATE ".$class::table()." SET";
            $f = true;
            foreach(get_object_vars($this) as $key=>$val)
            {
                if($f)
                {
                    $query .= ' '.$key.'="'.$val.'"';
                    $f = false;
                }
                else
                {
                    $query .= ', '.$key.'="'.$val.'"';
                }
            }
            $query .= " WHERE id=$this->id";
            $query = new Query($query, $class);
            return $query->exe();
        }
    }




    public function delete()
    {
        if($this->id)
        {
            $class = get_called_class();
            $query = new Query("DELETE FROM ".$class::table()." WHERE id=$this->id", $class);
            return $query->exe();
        }
        else throw new \Exception("Cannot delete unsaved object");
    }
}


?>
