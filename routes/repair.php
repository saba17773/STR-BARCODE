<?php

$app->get("/repair_income", "App\Controllers\RepairIncomeController::index");
$app->post("/api/v1/repair_income/save", "App\Controllers\RepairIncomeController::save");


$app->get("/repair_outcome", "App\Controllers\RepairOutcomeController::index");
$app->post("/api/v1/repair_outcome/save", "App\Controllers\RepairOutcomeController::save");
