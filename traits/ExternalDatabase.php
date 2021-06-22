<?php

trait ExternalDatabase
{
    public function external_query($query)
    {
        global $external_db;
        global $database;
        $conn = new mysqli(
            $database['host'], 
            $database['user'], 
            $database['password'], 
            $external_db
        );
        $conn->query("set names 'utf8'");
        $res = $conn->query($query);
        $outcomes = Array();
        if(is_bool($res)){
            return [];
        }
        while($row = $res->fetch_assoc())
        {
            array_push($outcomes, (object)$row);
        }
        return $outcomes;
    }
}

?>