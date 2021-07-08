<?php

require_once 'engine/General.php';
use engine\General;

//load settings.json file
$settings = General\General::get_settings();

//load packages
General\General::load_packages();

//handleing CORS settings
General\General::cors($settings);

//extract current url
$current_url = General\General::extract_url();

//run matching url
require_once 'app/urls.php';
General\General::match_and_run($current_url, $urls);

?>