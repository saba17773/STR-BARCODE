<?php

$app->get('/generate_component_tag', 'App\V2\Component\ComponentController::generateComponentTag');
$app->post('/api/v2/component/get_component_tag_last_number', 'App\V2\Component\ComponentController::getLastNumberByDate');
$app->get('/component_tag/([^/]+)/([0-9]+)', 'App\V2\Component\ComponentController::printComponentTag');