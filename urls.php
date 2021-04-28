<?php
require_once 'engine/url.php';

$urls = [
    //user
    url('/', 'QuestionnaireView', 'index'),
    url('/questionnaires/single', 'QuestionnaireView', 'single'),
    url('/questionnaires/answers/store', 'QuestionnaireView', 'post', 'POST'),
    //auth
    url('/auth/login', 'AuthView', 'login'),
    url('/auth/login', 'AuthView', 'login', 'POST'),
    url('/auth/logout', 'AuthView', 'logout'),
    //admin
    url('/dashboard', 'DashboardView', 'index'),
    url('/dashboard/questionnaires/single', 'DashboardView', 'single_questionnaire'),
    url('/dashboard/questionnaires/add', 'DashboardView', 'create_questionnaire', 'POST'),
    url('/dashboard/questionnaires/delete', 'DashboardView', 'delete_questionnaire', 'POST'),
    url('/dashboard/questionnaires/edit', 'DashboardView', 'edit_questionnaire', 'POST'),

    url('/dashboard/questionnaires/questions/add', 'DashboardView', 'create_question', 'POST'),
    url('/dashboard/questionnaires/questions/delete', 'DashboardView', 'delete_question', 'POST'),
    url('/dashboard/questionnaires/questions/edit', 'DashboardView', 'edit_question', 'POST')
];

?>