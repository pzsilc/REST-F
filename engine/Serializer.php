<?php
namespace engine\Serializer;

abstract class Serializer
{
    //name of target model class
    const MODEL = null;

    //array of objects / object of type MODEL
    public $instance = null;

    //data of new object (i.e. for create)
    public $data = [];

    //errors which are appearing during is_valid method
    public $errors = [];




    public function __construct($instance, $_data = [])
    {
        $class = get_called_class();
        //additional fields for validating
        $this->fields();
        $this->instance = $instance;
        $this->data = [];
        //validate data arg
        if($_data && is_array($_data))
        {
            $this->data = $_data;
        }
        //getting relations
        if($class::MODEL)
        {
            $this->related();
        }
        if($this->instance)
        {
            $this->instance_to_array();
        }
    }


    //get appropriate fields for certain serializer basic on model reference.
    //if model exists (not null) this method is gettin fields from model's conatructor
    //else is gettin fields from $this->fields() method
    private function get_appropriate_fields($class)
    {
        if($class::MODEL)
        {
            //with defined model
            $model = $class::MODEL;
            $sample = new $model();
            return get_object_vars($sample);
        }
        else
        {
            //with no defined model
            $attrs = get_object_vars($this);
            unset($attrs['data']);
            unset($attrs['instance']);
            unset($attrs['errors']);
            return $attrs;
        }
    }


    //check if data inside 'data' attribute are valid with requirements of each field
    //that method doesn't consider any additional field, only appropriate fields
    //(model construct if model is defined or fields got from fields() method)
    public function is_valid()
    {
        $this->errors = [];
        $attrs = [];
        $class = get_called_class();
        $attrs = $this->get_appropriate_fields($class);
        $flag = true;
        foreach($attrs as $key => $value)
            if(!$this->$key->is_valid($this->data[$key], $class::MODEL, $this->errors)){
                $flag = false;
            }
        return $flag;
    }


    //create or update object, is depentent on $instance attr
    public function save()
    {
        $class = get_called_class();
        if(!$class::MODEL)
            throw new Exception("Class ".$class::MODEL." has no model defined. Cannot create object basic on data attribute");
        $attrs = $this->get_appropriate_fields($class);
        $model = $class::MODEL;
        if($this->instance && get_class($this->instance) === $model)
        {
            foreach($attrs as $key => $value)
            {
                $this->instance->$key = $value;
            }
            $this->instance->save();
            return $this->instance;
        }
        else
        {
            $obj = new $model();
            foreach($attrs as $key => $value)
            {
                $obj->$key = $value;
            }       
            $obj->save();
            return $obj;
        }
    }


    //delete $this->instance, returns false is instance is empty : else returns true
    public function delete()
    {
        if($this->instance && get_class($this->instance) === $model)
        {
            $this->instance->delete();
            return true;
        }
        return false;
    }



    public function instance_to_array()
    {
        if(is_array($this->instance))
        {
            $this->instance = array_map(function($obj){
                return (array)$obj;
            }, $this->instance);
        }
        else
        {
            $this->instance = (array)$this->instance;
        }
    }


    //create relations for foreignfields - get 1 argument which is assoc array with 1 element 'existing_attr' => 'new_attr'. Basic on existing_attr, create new which is related object 
    public function ref($transform_array)
    {
        if(!is_array($transform_array) || count($transform_array) !== 1)
        {
            throw new Exception("'ref' function has invalid format. Argument should be an array with exactly 1 element which is associate element 'existing_prop' => 'new_prop'");
        }

        $new_key = reset($transform_array);
        $old_key = key($new_key);

        $class = get_called_class();
        $attrs = $this->get_appropriate_fields($class);
        foreach($attrs as $key => $value)
        {
            if($key === $old_key)
            {
                //appropriate field
                if(get_class($value) !== 'ForeignField')
                {
                    throw new Exception("$old_key field is not instances of ForeignField");
                }
                else
                {
                    $model = $value->model;
                    foreach($this->instance as $sample)
                    {
                        $sample->$new_key = $model::get($sample->$old_key);
                    }
                }
            }
        }
    }


    //define own additional fields
    public function fields(){}

    //define relations with ref method
    public function relations(){}
}


?>