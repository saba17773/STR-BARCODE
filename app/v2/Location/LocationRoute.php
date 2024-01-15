<?php


$app->get('/p2/api/all_location', '\App\V2\Location\LocationController::getAllLocation');
$app->get('/api/v2/location_by_type', '\App\V2\Location\LocationController::getLocationByType');