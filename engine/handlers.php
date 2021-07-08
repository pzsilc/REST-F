<?php

function response($data, $status = 200)
{
    if(!$data || !is_array($data) || is_object($data[0])){
        throw new Exception('response function accepts only array as first argument');
    }
    http_response_code($status);
    echo json_encode($data);
}

?>