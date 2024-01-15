<?php

$app->get('/scrap_component', 'App\ScrapComponent\ScrapComponentController::home');
$app->post('/scrap_component/save', 'App\ScrapComponent\ScrapComponentController::save');
$app->get('/scrap_component/all', 'App\ScrapComponent\ScrapComponentController::getAll');
$app->post('/scrap_component/cancel', 'App\ScrapComponent\ScrapComponentController::saveCancel');
$app->post('/scrap_component/complete', 'App\ScrapComponent\ScrapComponentController::saveComplete');