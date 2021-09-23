<?php
namespace core\Fields;

abstract class Field
{
    public $settings;

    protected function __construct($args)
    {
        $this->settings = $args;
    }

    public static function init($args = [])
    {
        $class = get_called_class();
        return new $class($args);
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!isset($this->settings['required']) || $this->settings['required'])
        {
            if(!$var)
            {
                array_push($errors, $name.' field is required');
                return false;
            }
        }
        elseif(!isset($this->settings['default']))
        {
            array_push($errors, $name.' must have default value');
            return false;
        }
        if(isset($this->settings['unique']) && $this->settings['unique'] && $class)
        {
            try
            {
                $rows = count($class::filter([$name, '=', $var]));
            }
            catch(\Exception $e)
            {
                $rows = 0;
                array_push($errors, $name.' has unique option but has no model defined. This is forbitten');
            }

            if(!$rows)
            {
                array_push($errors, $name.' has invalid format');
                return false;
            }
        }

        return true;
    }

    public abstract function to_sql();
}









class CharField extends Field
{
    public function __construct($args)
    {
        parent::__construct($args);
        if(!isset($this->settings['max_length']) || !is_integer($this->settings['max_length']))
            throw new \Exception("CharField requires 'max_length' setting");
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $name.' field must be a string');
            return false;
        }
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $name.' is too long');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class TextField extends Field
{
    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $name.' field must be a string');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "TEXT";
    }
}



class IntegerField extends Field
{
    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_numeric($var))
        {
            array_push($errors, $name.' field must be a integer, not '.gettype($var));
            return false;
        }
        if(isset($this->settings['max']) && $var > $this->settings['max'])
        {
            array_push($errors, $name.' field is too great');
            return false;
        }
        if(isset($this->settings['min']) && $var < $this->settings['min'])
        {
            array_push($errors, $name.' field is too low');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "INT";
    }
}



class DecimalField extends Field
{
    public function __construct($args)
    {
        parent::__construct($args);
        if(!isset($this->settings['precision']) || !isset($this->settings['decimal_point']))
            throw new \Exception("DecimalField requires 'precision' and 'decimal_point' settings");
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_float($var))
        {
            array_push($errors, $name.' field must be a integer');
            return false;
        }
        if(isset($this->settings['max']) && $var > $this->settings['max'])
        {
            array_push($errors, $name.' field is too great');
            return false;
        }
        if(isset($this->settings['min']) && $var < $this->settings['min'])
        {
            array_push($errors, $name.' field is too low');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "DECIMAL(".$this->settings['precision'].' '.$this->settings['decimal_point'].")";
    }
}



class BooleanField extends Field
{
    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_bool($var))
        {
            array_push($errors, $name.' field must be a boolean');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "BOOLEAN";
    }
}



class EmailField extends Field
{
    public function __construct($args)
    {
        parent::__construct(array_merge($args, ['unique' => true]));
        if(!isset($this->settings['max_length']) || !is_integer($this->settings['max_length']))
            throw new \Exception("EmailField requires 'max_length' setting ");
    }

    private function check_email($email) {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.');
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $name.' field must be a integer');
            return false;
        }
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $name.' field is too long');
            return false;
        }
        if($this->check_email($var))
        {
            array_push($errors, $name.' field has invalid format');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class PasswordField extends Field
{
    public function __construct($args)
    {
        parent::__construct($args);
        if(!isset($this->settings['max_length']) || !is_integer($this->settings['max_length']))
            throw new \Exception("PasswordField requires 'max_length' setting");
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $name.' field must be a integer');
            return false;
        }
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $name.' field is too long');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class DateField extends Field
{
    const NOW = 'now';

    public function __construct($args)
    {
        parent::__construct($args);
        if(isset($this->settings['default']) && $this->settings['default'] === self::NOW)
            $settings['default'] = 'NOW()';
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!DateTime::createFromFormat('Y-m-d', $var))
        {
            array_push($errors, $name.' field has invalid format');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "DATE DEFAULT ".$settings['default'];
    }
}



class DateTimeField extends Field
{
    const NOW = 'now';

    public function __construct($args)
    {
        parent::__construct($args);
        if(isset($this->settings['default']) && $this->settings['default'] === self::NOW)
            $settings['default'] = 'NOW()';
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!\DateTime::createFromFormat('Y-m-d H:i:s', $var))
        {
            array_push($errors, $name.' field has invalid format');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        $res = "DATETIME";
        if(isset($this->settings['default'])){
            $res .= " DEFAULT ".$this->settings['default'];
        }
        return $res;
    }
}



class FileField extends Field
{
    public function __construct($args)
    {
        parent::__construct($args);
        if(!isset($this->settings['accept']))
        {
            $this->settings['accept'] = '__all__';
        }
        if(!is_array($this->settings['accept']) && !$this->settings['accept'] === '__all__')
        {
            throw new \Exception("Accept argument has to be an array or string with '__all__' value");
        }
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        $arr = explode('.', $var);
        $ext = $arr[count($arr) - 1];
        if($this->settings['accept'] !== '__all__')
        {
            if(!in_array($ext, $this->settings['accept']))
            {
                array_push($errors, 'Passed file with wrong extension. Appropriate extensions are: '.implode(', ', $this->settings['accept']));
                return false;
            }
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "VARCHAR (256)";
    }
}



class JSONField extends Field
{
    public function is_valid($name, $var, $class, &$errors)
    {
        json_decode($var);
        if(json_last_error() !== JSON_ERROR_NONE)
        {
            array_push($errors, "$name field is not valid JSON string");
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "TEXT";
    }
}



class ForeignField extends Field
{
    public $model = null;

    public static function init($model = null, $args = [])
    {
        if(!is_array($args) || !$model)
            throw new \Exception('Invalid args');
        $o = new ForeignField($args);
        $o->model = $model;
        return $o;
    }

    public function is_valid($name, $var, $class, &$errors)
    {
        if(!is_numeric($var))
        {
            array_push($errors, 'First argument should be a numeric');
            return false;
        }
        $var = intval($var);
        $model = $this->model;
        $sample = $model::get(['id', '=', $var]);

        if(!boolval($sample))
        {
            array_push($errors, 'Association of '.$name.' field doesn\'t exists');
            return false;
        }
        return parent::is_valid($name, $var, $class, $errors);
    }

    public function to_sql()
    {
        return "INT UNSIGNED";
    }
}







?>
