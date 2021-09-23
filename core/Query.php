<?php
namespace core\Query;

class Query
{
    private $query = "";
    private $target_class = "";



    public function __construct($query, $target_class)
    {
        $this->query = $query;
        $this->target_class = $target_class;
    }



    public function exe()
    {
        $class = explode('\\', $this->target_class)[2];
        eval('$db = \\'.$this->target_class.'::DB;');
        global $settings;
        if(!$db) $db = $settings['defaultDatabase'];
        $databases = $settings['databases'];
        $databases = array_filter($databases, function($_db) use ($db){
            return $_db->alias === $db;
        });
        if(!$databases) throw new \Exception("Wrong database name for $class model");
        $database = end($databases);
        $conn = new \mysqli(
            $database->host,
            $database->user,
            $database->password,
            $database->db
        );

	    $conn->query("set names 'utf8'");
        $results = $conn->query($this->query);
        if($results === true)
        {
            return $conn->insert_id;
        }
        elseif($results === false)
        {
            http_response_code(500);
            json_encode([
                'type' => 'error',
                'data' => 'Failure to execute query '.$this->query
            ]);
            die();
        }
        else
        {
            $arr = [];
            $class = $this->target_class;
            while($row = $results->fetch_assoc())
            {
                $obj = new $class();
                foreach($row as $key=>$val)
                {
                    $obj->$key = $val;
                }
                array_push($arr, $obj);
            }
            return $arr;
        }
    }




    public function _and()
    {
        $this->query .= ' AND ';
        return $this;
    }



    public function _or()
    {
        $this->query .= ' OR ';
        return $this;
    }



    public function filter(...$arr)
    {
        $_arr = [];
        foreach($arr as $elem) {
            if(count($elem) != 3) throw new \Exception('Invalid input '.implode(', ', $elem));
            array_push($_arr, $elem[0]." ".$elem[1]." '".$elem[2]."'");
        }
        $cond = implode(' AND ', $_arr);
        $this->query .= " ($cond) ";
        return $this;
    }



    public function order_by($key, $way = 'ASC')
    {
        $this->query .= " ORDER BY '$key' $way";
        return $this;
    }



    public function paginate($page = 1, $num_on_page = 30)
    {
        $start_at = $num_on_page * ($page - 1);
        $this->query .= " LIMIT $num_on_page OFFSET $start_at";
        return $this;
    }
}

?>
