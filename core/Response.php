<?php
namespace core\Response;

class Response
{
    private $data = [];
    private $status_code = 200;

    public function __construct($data = [], $status_code = 200)
    {
        if(!is_array($data))
        {
            throw new Exception("First argument of Response constructor should be an array with data.");
        }
        $this->data = $data;
        $this->status_code = $status_code;
        http_response_code($this->status_code);
        echo json_encode($this->data);
        exit();
    }
}

?>