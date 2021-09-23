<?php
namespace core\Serializer;
use core\Fields\Field;
use core\Request\Request;

abstract class Serializer
{
    //name of target model class
    const MODEL = null;

    //fields
    const FIELDS = [];

    //fields that only for read (returned with list or retrieve)
    const READ_ONLY = [];

    //fields that only for write (returned with create or update)
    const WRITE_ONLY = [];

    //array of objects / object of type MODEL
    public $instance = null;

    //
    private $validated = false;

    //data of new object (i.e. for create)
    private $_data = [];

    //for multi objects required to be true
    private $many = false;

    //errors which are appearing during is_valid method
    private $errors = [];

    //request
    private $request = null;




    public function __construct($instance=null, $data=[], $many=false)
    {
        $model = (get_called_class())::MODEL;
        if($instance)
            if($many){
                if(!is_array($instance))
                    throw new \Exception('You must pass instance as array when you are passing many=true, not '.gettype($instance));
            } else {
                if(!$instance instanceof $model)
                    throw new \Exception('You must pass instance as single object of type '.$model.' when you are passing many=false, not '.gettype($instance));
            }
        if($data && $many)
            throw new \Exception('You cannot pass data and many=true parameters together');
        if(!$data)
            $this->validated = true;
        $this->instance = $instance;
        $this->_data = $data;
        $this->many = $many;
    }



    private function get_fields_of_model()
    {
        $class = get_called_class();
        $model = $class::MODEL;
        $obj = new $model();
        $arr = [];
        foreach(get_object_vars($obj) as $key => $val){
            if($val instanceof Field)
                $arr[$key] = $val;
        }
        return $arr;
    }



    private function get_additional_fields()
    {
        $arr = [];
        foreach(get_object_vars($this) as $key => $val){
            if($val instanceof Field)
                $arr[$key] = $val;
        }
        return $arr;
    }



    private function get_fields($action='read')
    {
        $class = get_called_class();
        $fields = $this->get_fields_of_model();
        $add_fields = $this->get_additional_fields();
        $fields = array_merge($fields, $add_fields);
        foreach($fields as $key => $val){
            if(!in_array($key, $class::FIELDS))
                unset($fields[$key]);
        }
        switch($action){
             case 'read': {
                foreach($fields as $key => $val){
                    if(in_array($key, $class::WRITE_ONLY))
                        unset($fields[$key]);
                    }
             }; break;
             case 'write': {
                 foreach($fields as $key => $val){
                     if(in_array($key, $class::READ_ONLY))
                         unset($fields[$key]);
                     }
             }; break;
        }
        return $fields;
    }



    public function __get($name)
    {
        function filter_by_fields($obj, $fields){
            foreach(get_object_vars($obj) as $key => $val){
                if($key !== 'id' && !in_array($key, array_keys($fields)))
                    unset($obj->$key);
            }
            return $obj;
        }

        if($name !== 'data')
            return $this->$name;
        else{
            if(!$this->validated)
                throw new \Exception('You must validate data first with is_valid function, then you can get it.');
            if($this->instance){
                if($this->many){
                    return array_map(function($o){
                        return (array)filter_by_fields($o, $this->get_fields('read'));
                    }, $this->instance);
                }
                else {
                    return (array)filter_by_fields($this->instance, $this->get_fields('read'));
                }
            }
            else return (array)$this->_data;
        }
    }



    public function is_valid()
    {
        if(!$this->_data){
            $this->errors[] = 'You cannot call is_valid method if you had empty data array';
            return false;
        }
        $fields = $this->get_fields('write');
        foreach($fields as $key => $val){
            if(!isset($this->_data[$key])){
                $this->errors[] = 'data that you passeed has no '.$key.' field';
                return false;
            }
            if(!$val->is_valid($key, $this->_data[$key], get_called_class(), $this->errors)){
                $this->errors[] = 'value for '.$key.' field is incorrect';
                return false;
            }
        }
        $this->validated = true;
        return true;
    }



    public function create($validated_data)
    {
        $model = (get_called_class())::MODEL;
        $obj = new $model();
        foreach($validated_data as $key => $val)
            $obj->$key = $val;
        return $obj->save();
    }



    public function update($instance, $validated_data)
    {
        foreach($validated_data as $key => $val)
            $instance->$key = $val;
        return $instance->save();
    }



    public function destroy($instance)
    {
        return $instance->delete();
    }



    public function save()
    {
        if(!$this->_data)
            throw new \Exception('You cannot save object which doesnt exist');
        if(!$this->validated)
            throw new \Exception('You cannot save object with data that are not validated');
        if($this->instance){
            return $this->update($this->instance, $this->_data);
        } else {
            return $this->create($this->_data);
        }
    }



    public function delete()
    {
        if(!is_object($this->instance))
            throw new \Exception('If you want to delete object you must provide valid object as instance, not '.gettype($this->instance));
        return $this->destroy($this->instance);
    }



    public function fields(){}
}


?>
