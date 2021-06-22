<?php
require_once 'engine/url.php';

$urls = [
    //auth
    url('/backend/auth/login', 'AuthView', 'login', 'POST'),
    url('/backend/user/holidays', 'UserView', 'get_holidays', 'POST'),
    //user resources
    url('/backend/user/employees', 'UserView', 'get_employees', 'POST'),
    url('/backend/user/managers', 'UserView', 'get_manager', 'POST'),
    //holidays
    url('/backend/holidays/get-by-employee-id', 'HolidaysView', 'get', 'POST'),
    url('/backend/holidays/add', 'HolidaysView', 'post', 'POST'),
    url('/backend/holidays/update', 'HolidaysView', 'put', 'POST'),
    url('/backend/holidays/delete', 'HolidaysView', 'delete', 'POST'),
    //statusy
    url('/backend/statuses', 'StatusView', 'get'),
    //pracownicy
    url('/backend/employees', 'DashboardView', 'get_employees', 'POST'),
    //powiadomienia
    url('/backend/notifications', 'NotificationView', 'get', 'POST'),
    url('/backend/notifications/update', 'NotificationView', 'put', 'POST'),
    //rodzaje
    url('/backend/kinds', 'HolidayKindView', 'get'),
    //dokumenty
    url('/backend/pdf', 'PDFView', 'post', 'POST')
];

?>