<?php

$app->get("/change_password", "App\Controllers\ProfileController::changePassword");

// API
$app->post("/api/v1/user/change_password", "App\Controllers\ProfileController::updatePassword");