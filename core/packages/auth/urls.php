<?php

use core\URL\URL;
use core\packages\auth\views;

$urls = [
    URL::add('/get-user/', views\UserAPIView::_get_user()),
    URL::add('/login/', views\LoginAPIView::_login(), URL::POST),
    URL::add('/logout/', views\LogoutAPIView::_logout(), URL::POST)
];

?>