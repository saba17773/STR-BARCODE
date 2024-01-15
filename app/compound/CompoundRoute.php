<?php

$app->get("/Page_compound/coumpounmaster", "App\compound\CompoundController::coumpounmaster");
$app->get("/Page_compound/compoundMc", "App\compound\CompoundController::compoundMc");
$app->get("/Page_compound/CompoundMcPallet/([^/]+)", "App\compound\CompoundController::CompoundMcPallet");
$app->get("/Page_compound/CompoundPress", "App\compound\CompoundController::CompoundPress");
$app->get("/Page_compound/CompoundNoPress", "App\compound\CompoundController::CompoundNoPress");

$app->get("/api/compound/all", "App\compound\CompoundController::allMovementType");
$app->get("/api/compound/remillandMix/([^/]+)", "App\compound\CompoundController::remillandMix");
$app->get("/api/compound/Mc", "App\compound\CompoundController::Mc");
$app->get("/api/compound/Compound_Code/([^/]+)", "App\compound\CompoundController::Compound_Code");
$app->post("/api/Compound/save", "App\compound\CompoundController::save");
$app->post("/api/Compound/savePallet", "App\compound\CompoundController::savePallet");
$app->post("/api/Compound/deleteCompound/([^/]+)", "App\compound\CompoundController::deleteCompound");
$app->post("/api/Compound/updatestatus/([^/]+)", "App\compound\CompoundController::updatestatus");
$app->post("/api/Compound/tb/([^/]+)", "App\compound\CompoundController::tb");
