<?php

namespace core\Settings;

class Settings
{
    public static function __callStatic($method, $_)
    {
        $settings_file = file_get_contents(__dir__.'/../server/settings.json');
        $content = json_decode($settings_file);
        $var = substr($method, 4);
        return $content->$var;
    }
}

?>