<?php

$app->get("/loading/deny", "App\V2\Loading\LoadingController::denyLoading");
$app->post("/loading/add_deny_loading", "App\V2\Loading\LoadingController::saveAddLoading");
$app->post("/loading/delete_deny_loading", "App\V2\Loading\LoadingController::saveDeleteDenyLoading");
$app->get("/loading/export", "App\V2\Loading\LoadingController::export");
