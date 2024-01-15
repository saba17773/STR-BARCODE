<?php

$app->get("/rate/except", "App\V2\Rate\RateController::index");
$app->post("/rate/except/save", "App\V2\Rate\RateController::saveExceptBarcode");
