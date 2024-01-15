<?php

$app->get("/itemset", "App\Controllers\ItemSetController::index");
$app->get("/itemset/print/item/([^/]+)", "\App\Controllers\ItemSetController::printItem");

// API
$app->get("/api/v1/itemset/all", "App\Controllers\ItemSetController::fetchAll");
$app->post("/api/v1/itemset/save", "\App\Controllers\ItemSetController::save");
$app->post("/api/v1/itemset/update", "\App\Controllers\ItemSetController::update");