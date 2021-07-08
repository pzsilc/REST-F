<?php
namespace engine\Fields;

abstract class Field
{
    public $name;
    public $settings;

    protected function __construct($name, $settings)
    {
        $this->name = $name;
        $this->settings = $settings;
    }

    public static function init($name, $settings=[])
    {
        $class = get_called_class();
        if(!is_array($settings))
            throw new Exception('Invalid args');
        return new $class($name, $settings);
    }

    public function is_valid($var, $class, $errors)
    {
        if(!isset($this->settings['required']) || $this->settings['required'])
        {
            if(!$var)
            {
                array_push($errors, $this->name.' field is required');
                return false;
            }
        }
        elseif(!isset($this->settings['default']))
        {
            array_push($errors, $this->name.' must have default value');
            return false;
        }
        if(isset($this->settings['unique']) && $this->settings['unique'] && $class)
        {
            try
            {
                $rows = count($class::filter([$this->name, '=', $var]));
            }
            catch(Exception $e)
            {
                $rows = 0;
                array_push($errors, $this->name.' has unique option but has no model defined. This is forbitten');
            }

            if(!$rows)
            {
                array_push($errors, $this->name.' has invalid format');
                return false;
            }
        }

        return true;
    }

    public abstract function to_sql();
}









class CharField extends Field
{
    public function __construct($name, $settings)
    {
        parent::__construct($name, $settings);
        if(!isset($settings['max_length']) || !is_integer($settings['max_length']))
            throw new Exception("CharField requires 'max_length' setting");
    }

    public function is_valid($var, $class, $errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $this->name.' field must be a string');
            return false;
        } 
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $this->name.' is too long');
            return false;
        }
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class TextField extends Field
{
    public function is_valid($var, $class, $errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $this->name.' field must be a string');
            return false;
        } 
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "TEXT";
    }
}



class IntegerField extends Field
{
    public function is_valid($var, $class, $errors)
    {
        if(!is_integer($var))
        {
            array_push($errors, $this->name.' field must be a integer');
            return false;
        }
        if(isset($this->settings['max']) && $var > $this->settings['max'])
        {
            array_push($errors, $this->name.' field is too great');
            return false;
        }
        if(isset($this->settings['min']) && $var < $this->settings['min'])
        {
            array_push($errors, $this->name.' field is too low');
            return false;
        }
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "INT";
    }
}



class DecimalField extends Field
{
    public function __construct($name, $settings)
    {
        parent::__construct($name, $settings);
        if(!isset($settings['precision']) || !isset($settings['decimal_point']))
            throw new Exception("DecimalField requires 'precision' and 'decimal_point' settings");
    }

    public function is_valid($var, $class, $errors)
    {
        if(!is_float($var))
        {
            array_push($errors, $this->name.' field must be a integer');
            return false;
        }
        if(isset($this->settings['max']) && $var > $this->settings['max'])
        {
            array_push($errors, $this->name.' field is too great');
            return false;
        }
        if(isset($this->settings['min']) && $var < $this->settings['min'])
        {
            array_push($errors, $this->name.' field is too low');
            return false;
        }
        return parent::is_valid($var, $class, $errors);
    }
    
    public function to_sql()
    {
        return "DECIMAL(".$this->settings['precision'].' '.$this->settings['decimal_point'].")";
    }
}



class BooleanField extends Field
{
    public function is_valid($var, $class, $errors)
    {
        if(!is_bool($var))
        {
            array_push($errors, $this->name.' field must be a boolean');
            return false;
        }
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "BOOLEAN";
    }
}



class EmailField extends Field
{
    public function __construct($name, $settings)
    {
        $settings['unique'] = true;
        parent::__construct($name, $settings);
        if(!isset($settings['max_length']) || !is_integer($settings['max_length']))
            throw new Exception("EmailField requires 'max_length' setting ");
    }

    private function check_email($email) {
        $find1 = strpos($email, '@');
        $find2 = strpos($email, '.');
        return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }

    public function is_valid($var, $class, $errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $this->name.' field must be a integer');
            return false;
        } 
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $this->name.' field is too long');
            return false;
        } 
        if($this->check_email($var))
        {
            array_push($errors, $this->name.' field has invalid format');
            return false;
        }
        return parent::is_valid($var, $class, $errors);    
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class PasswordField extends Field
{
    public function __construct($name, $settings)
    {
        parent::__construct($name, $settings);
        if(!isset($settings['max_length']) || !is_integer($settings['max_length']))
            throw new Exception("PasswordField requires 'max_length' setting");
    }

    public function is_valid($var, $class, $errors)
    {
        if(!is_string($var))
        {
            array_push($errors, $this->name.' field must be a integer');
            return false;
        } 
        if(strlen($var) > $this->settings['max_length'])
        {
            array_push($errors, $this->name.' field is too long');
            return false;
        } 
        return parent::is_valid($var, $class, $errors);    
    }

    public function to_sql()
    {
        return "VARCHAR(".$this->settings['max_length'].")";
    }
}



class DateField extends Field
{
    const NOW = 'now';

    public function __construct($name, $settings)
    {
        if(isset($settings['default']) && $settings['default'] === self::NOW)
            $settings['default'] = 'NOW()';
        parent::__construct($name, $settings);
    }

    public function is_valid($var, $class, $errors)
    {
        if(!DateTime::createFromFormat('Y-m-d', $var))
        {
            array_push($errors, $this->name.' field has invalid format');
            return false;
        } 
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "DATE DEFAULT ".$settings['default'];
    }
}



class DateTimeField extends Field
{
    const NOW = 'now';

    public function __construct($name, $settings)
    {
        if(isset($settings['default']) && $settings['default'] === self::NOW)
            $settings['default'] = 'NOW()';
        parent::__construct($name, $settings);
    }

    public function is_valid($var, $class, $errors)
    {
        if(!DateTime::createFromFormat('Y-m-d H:i:s', $var))
        {
            array_push($errors, $this->name.' field has invalid format');
            return false;
        }
        return parent::is_valid($var, $class, $errors);
    }

    public function to_sql()
    {
        return "DATETIME DEFAULT ".$settings['default'];
    }
}



class ForeignField extends Field
{
    public $model = null;

    public static function init($name, $model, $settings=[])
    {
        if(!is_array($settings) || !$model) 
            throw new Exception('Invalid args');
        $o = new ForeignField($name, $settings);
        $o->model = $model;
        return $o;
    }

    public function is_valid($var, $class, $errors)
    {
        if(!is_integer($var))
            return false;
        $model = $this->model;
        $sample = $model::get($var);
        if(!boolval($sample))
        {
            array_push($errors, 'Association of '.$this->name.' field doesn\'t exists');
            return false;
        } 
        return parent::is_valid($var, $class);
    }

    public function to_sql()
    {
        return "INT UNSIGNED";
    }
}







?>