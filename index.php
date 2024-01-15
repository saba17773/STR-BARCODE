<?php
// v1.0.0
ini_set('memory_limit', '512M');
set_time_limit(180);
session_start();

error_reporting(0);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once "./variables.php";
require_once "./vendor/autoload.php";
require_once "./libs/excel-reader/SpreadsheetReader.php";
require_once "./functions.php";

$app = new \Wattanar\Router();

// Test
$app->get("/test_dev", "App\Controllers\TestController::testAPI");

// Page
$app->get("/", "App\Controllers\PageController::welcome");
$app->get("/home", "App\Controllers\PageController::index");
$app->get("/checkdevice", "App\Controllers\PageController::checkdevicepage");
$app->get("/tracking", "App\Controllers\PageController::tracking_v2");

$app->get("/tracking_v3", "App\Controllers\PageController::tracking_v3");
$app->get("/final_login", "\App\Controllers\LoginController::finalLogin");
$app->get('/qa_reverse', '\App\Controllers\MovementController::qaReverse');

$app->get("/svo_pcr_tracking", "App\Controllers\PageController::svo_pcr_tracking");

//Rate
$app->get("/addUser", "App\Controllers\PageController::add_more_user");
$app->get("/api/User/ShowUser", "App\controllers\UserController::bindGrvUser");
$app->get("/api/User/ChkCountUser", "App\controllers\UserController::ChkCountUser");
$app->post("/api/User/chkUserLogin", "App\controllers\UserController::chkUserLogin");
$app->post("/api/User/chkUserLogin2/([^/]+)", "App\controllers\UserController::chkUserLogin2");
$app->post("/api/User/insertRate/([^/]+)", "App\controllers\UserController::insertRate");
$app->post("/api/User/insertMore/([^/]+)/([^/]+)/([^/]+)", "App\controllers\UserController::insertMore");
$app->post("/api/User/updateLogoutDate/([^/]+)/([^/]+)/([^/]+)", "App\controllers\UserController::updateLogoutDate");
$app->post("/api/User/logoutRequest", "App\controllers\UserController::logoutRequest");
$app->post("/api/User/logoutRequest2", "App\controllers\UserController::logoutRequest2");
$app->post("/api/User/chkBuildType/([^/]+)", "App\controllers\UserController::chkBuildType");
$app->post("/api/User/getMachine/([^/]+)", "App\controllers\UserController::getMachine");
$app->post("/api/User/getMachine2/([^/]+)", "App\controllers\UserController::getMachine2");
$app->post("/api/User/chkTypeMC", "App\controllers\UserController::chkTypeMC");
$app->post("/api/User/firstRows_Session/([^/]+)", "App\controllers\UserController::firstRows_Session");


$app->post("/api/greentire/buildtrans", "App\Controllers\GreentireController::insert_buildtrans");
$app->post("/api/greentire/total_ratetrans", "App\Controllers\GreentireController::total_ratetrans");

//import sch
$app->get("/rate/schbuild", "App\Controllers\PageController::BuildSchedule");
$app->post("/rate/schbuild_v2", "App\Controllers\ScheduleController::BuildSchedule_V2");
$app->get("/api/schbuild/bindGrid/([^/]+)/([^/]+)", "App\Controllers\ScheduleController::bindGrid");
$app->get("/api/schbuild/bindGridLine/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\ScheduleController::bindGridLine");
$app->get("/import/schbuild", "App\Controllers\ImportController::importBuildSchedule");
$app->post("/api/import/schbuild", "App\Controllers\ImportController::saveImportBuildSchedule");
$app->get("/download/schbuild/([^/]+)/([^/]+)", "App\Controllers\ScheduleController::downloadBuildSchedule");
// import cure
$app->get("/cure/importschure", "App\Controllers\PageController::CureSchedule");
$app->post("/cure/importschure_v2", "App\Controllers\ScheduleController::CureSchedule_V2");
$app->get("/import/schcure", "App\Controllers\ImportController::importCureSchedule");
$app->get("/download/schcure/([^/]+)/([^/]+)", "App\Controllers\ScheduleController::downloadBuildSchedule1");
$app->get("/api/schcure/cureGrid/([^/]+)/([^/]+)", "App\Controllers\ScheduleController::cureGrid");
$app->post("/api/import/schcure", "App\Controllers\ImportController::saveImportCureSchedule");

//Building Rate Report
$app->get("/api/line/Line_TBR", "App\Controllers\PressController::Line_TBR");
$app->get("/api/line/Line_PCR", "App\Controllers\PressController::Line_PCR");
$app->get("/report/ratebuilding", "App\Controllers\PageController::ReportRateBuilding");
$app->get("/report/actbuilding", "App\Controllers\PageController::ReportActBuilding");
$app->get("/report/logbuilding", "App\Controllers\PageController::ReportLogBuilding");
// $app->post("/api/pdf/ratebuilding", "App\Controllers\ReportController::genRatebuildingPDF");
$app->post("/api/pdf/ratebuilding", "App\Controllers\ReportController::genRatebuildingPDF_V2");
$app->post("/api/pdf/actbuilding", "App\Controllers\ReportController::genActbuildingPDF");
$app->post("/api/pdf/logbuilding", "App\Controllers\ReportController::genLogbuildingPDF");

$app->get("/api/mac/Building_TBR", "App\Controllers\PressController::Building_TBR");
$app->get("/api/mac/Building_PCR", "App\Controllers\PressController::Building_PCR");

//Deduct Rate
$app->get("/deduct", "App\Controllers\PageController::Deduct_Rate");
$app->get("/api/deduct/machineTBR", "App\Controllers\DeductController::machine_TBR");
$app->get("/api/deduct/machinePCR", "App\Controllers\DeductController::machine_PCR");
$app->get("/api/deduct/bindGrid/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\DeductController::bindGrid");
$app->post("/api/deduct/checkLog/([^/]+)/([^/]+)", "App\Controllers\DeductController::checkLog");
$app->post("/api/deduct/insertDeduct/([^/]+)/([^/]+)", "App\Controllers\DeductController::insertDeduct");
$app->post("/api/deduct/updateDeduct", "App\Controllers\DeductController::updateDeduct");
$app->get("/api/deduct/bindGridDeduct/([^/]+)", "App\Controllers\DeductController::bindGridDeduct");

//Deduct Report
$app->get("/report/deduct", "App\Controllers\PageController::ReportDeduct");
$app->get("/api/mac/Type_TBR", "App\Controllers\DeductController::Type_TBR");
$app->get("/api/mac/Type_PCR", "App\Controllers\DeductController::Type_PCR");
$app->get("/rptdeduct/bindGridEmp", "App\Controllers\DeductController::bindGridEmp");
$app->post("/api/pdf/deduct", "App\Controllers\ReportController::genDeductPDF");

//Rate Master
$app->get("/ratemaster", "App\Controllers\PageController::Rate_Master");
$app->get("/api/ratemaster/rategroup", "App\Controllers\RateMasterController::RateGroup");
$app->get("/api/ratemaster/bindGridBuild1/([^/]+)/([^/]+)", "App\Controllers\RateMasterController::bindGridBuild1");
$app->get("/api/ratemaster/bindGridBuild2/([^/]+)/([^/]+)", "App\Controllers\RateMasterController::bindGridBuild2");
$app->get("/api/ratemaster/getMachine/([^/]+)", "App\Controllers\RateMasterController::getMachine");
$app->post("/api/ratemaster/insertBuild_Builder/([^/]+)", "App\Controllers\RateMasterController::insertBuild_Builder");
$app->post("/api/ratemaster/insertBuild_ChangeCode/([^/]+)", "App\Controllers\RateMasterController::insertBuild_ChangeCode");
$app->post("/api/ratemaster/updateBuild_Builder", "App\Controllers\RateMasterController::updateBuild_Builder");
$app->post("/api/ratemaster/updateBuild_ChangeCode", "App\Controllers\RateMasterController::updateBuild_ChangeCode");
$app->post("/api/ratemaster/getMachineType/([^/]+)", "App\Controllers\RateMasterController::getMachineType");
$app->get("/api/ratemaster/bindGridPLY/([^/]+)", "App\Controllers\RateMasterController::bindGridPLY");
$app->get("/api/ratemaster/bindGridPLY2/([^/]+)", "App\Controllers\RateMasterController::bindGridPLY2");
$app->get("/api/ratemaster/getMac", "App\Controllers\RateMasterController::getMac");
$app->post("/api/ratemaster/insertCure/([^/]+)", "App\Controllers\RateMasterController::insertCure");
$app->get("/api/ratemaster/bindGridCure", "App\Controllers\RateMasterController::bindGridCure");
$app->post("/api/ratemaster/updateCureByMachine/([^/]+)", "App\Controllers\RateMasterController::updateCureByMachine");

//RateMaster_V2
$app->get("/api/ratemaster/getPayment", "App\Controllers\RateMasterController::getPayment");
$app->get("/api/ratemaster/bindGrid_SEQ", "App\Controllers\RateMasterController::bindGrid_SEQ");
$app->get("/api/ratemaster/getDataBYSeqGrpID/([^/]+)", "App\Controllers\RateMasterController::getDataBYSeqGrpID");

//RateMonthly Report
$app->get("/report/rate_monthly", "App\Controllers\PageController::ReportRateMonthly");
$app->post("/api/pdf/rate_month", "App\Controllers\ReportController::genRateMonthlyPDF");

//Curing Rate Report
$app->get("/report/ratecuring", "App\Controllers\PageController::ReportRateCuring");
$app->post("/api/pdf/ratecuring", "App\Controllers\ReportController::genRatecuringPDF_V2");

//Scrap Checking
$app->get("/scrapcheck", "App\Controllers\PageController::ScrapCheck");
$app->post("/scrapchk", "App\Controllers\ScrapController::scrap_check");

//Scrap Checking Report
$app->get("/report/scrapchecking", "App\Controllers\ReportController::ScrapChecking");
$app->post("/report/ScrapCheckingPdf", "App\Controllers\ReportController::ScrapCheckingPdf");

// $app->get("/tracking_v2", "App\Controllers\PageController::tracking_v2");
$app->get("/barcode/printing", "App\Controllers\PageController::barcodePrinting");
$app->get("/barcode/curing", "App\Controllers\PageController::barcodeCuring");
$app->get("/curing", "App\Controllers\PageHandheldController::curingHandheld");
$app->get("/curing_no_serial", "App\Controllers\PageHandheldController::curingHandheldWithoutSerial");
$app->get("/loading/desktop", "App\Controllers\PageController::loadingDesktop");
$app->get('/loading/mobile', 'App\Controllers\PageController::loadingMobile');
$app->get("/stocktaking", "App\Controllers\PageController::stockTaking");
$app->get("/xray/issue/wh", "App\Controllers\PageController::xrayIssueWH");
$app->get("/warehouse/incoming", "App\Controllers\PageController::warehouseIncoming");
$app->get("/template/register", "App\Controllers\PageController::templateRegister");
$app->get("/greentire/incoming", "App\Controllers\PageController::greentireIncoming");
$app->get("/greentire/incoming_old", "App\Controllers\PageController::greentireIncomingOld");
$app->get("/xray/issue", "App\Controllers\PageController::xray");
$app->get("/movement/issue", "App\Controllers\PageController::movementIssue");
$app->get("/fifobatch/fifobatchmaster", "App\Controllers\PageController::fifobatch");
$app->post("/fifobatch/insertData/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\FIFOBatchController::insertData");
$app->post("/fifobatch/updateData/([^/]+)/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\FIFOBatchController::updateData");
$app->post("/fifobatch/chkProductGrp/([^/]+)", "App\Controllers\FIFOBatchController::chkProductGrp");
// $app->get("/ReturnCause/issue", "App\Controllers\PageController::DetailReturncause");
$app->get("/movement/issue_returncause", "App\Controllers\PageController::issue_returncause");
$app->get("/landing", "App\Controllers\PageController::landing");
$app->get("/warehouse_type", "App\Controllers\PageController::warehouseType");
$app->get("/final/incoming", "App\Controllers\PageController::finalIncoming");
$app->get("/greentire/incomingcheck", "App\Controllers\PageController::GreentireCheckQc");
$app->get("/movement/issue/new", "App\Controllers\PageController::movementIssueNew");
$app->get("/movement/issue/re_cause", "App\Controllers\PageController::re_cause");
$app->get('/movement/reverse', "App\Controllers\PageController::movementReverse");
$app->get('/final_return', 'App\Controllers\PageController::finalReturn');
$app->get('/adjust', 'App\Controllers\PageController::adjust');
$app->get('/master/actions', 'App\Controllers\PageController::actions');
$app->get('/ismobile', "App\Components\Utils::isMobile");
// logLogin
$app->get("/logreportLogin", "App\Controllers\PageController::logLogin");
$app->get("/api/LogLogin/all", "App\controllers\UserController::APIloglogin");
// Barcode Generator
$app->get("/generator/([^/]+)", "App\Controllers\BarcodeController::generator");
$app->get("/generator/user/([^/]+)/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\UserController::genUserBarcode");

// not A5
$app->get("/generator/greentire/([^/]+)", "App\Controllers\BarcodeController::genGreentireCode");
$app->get("/generator/building/([^/]+)", "App\Controllers\BuildingController::genBuildingCode");
// ======= //

// A5
$app->get("/generator/greentire/a5/([^/]+)", "App\Controllers\BarcodeController::genGreentireCodeA5");
$app->get("/generator/building/a5/([^/]+)", "App\Controllers\BuildingController::genBuildingCodeA5");
$app->get("/generator/curetire/a5/([^/]+)/([^/]+)", "App\Controllers\BarcodeController::genCuretireA5");
// ======= //

$app->get("/generator/curing/([^/]+)", "App\Controllers\CuringController::genCuringCode");
$app->get("/template/generator/([^/]+)/([^/]+)", "App\Controllers\TemplateController::generate");
$app->get("/serial/print/([^/]+)/([^/]+)", "App\Controllers\TemplateController::printSerial");
// Print Movement Issue
$app->get("/movement_issue/print/([^/]+)", "App\Controllers\MovementController::printIssueByJournalID");
$app->get("/movement_issue/printsum/([^/]+)", "App\Controllers\MovementController::printIssueByJournalIDSummary");
// Print Movement listLine
$app->get("/movement_issue/printlist/([^/]+)", "App\Controllers\MovementController::printIssueByJournalLine");
// Print returnCause Issue
$app->get("/returncause_issue/print/([^/]+)", "App\Controllers\ReturnCauseController::printReturnByReturnID");

// hold & repair
$app->get("/hold", "App\Controllers\PageController::hold");
$app->get("/unhold", "App\Controllers\PageController::unhold");
$app->get("/repair", "App\Controllers\PageController::repair");
$app->get("/unrepair", "App\Controllers\PageController::unrepair");
$app->get("/greentire/scrap", "App\Controllers\PageController::scarp");
$app->get("/lightbuff", "App\Controllers\PageController::lightbuff");


// Master
$app->get("/master/greentirecode", "App\Controllers\PageController::masterGreentireCode");
$app->get("/master/building", "App\Controllers\PageController::masterBuilding");
$app->get("/master/BOI", "App\Controllers\PageController::boi");
$app->get("/master/press", "App\Controllers\PageController::masterPress");
$app->get("/master/mold", "App\Controllers\PageController::masterMold");
$app->get("/master/curetirecode", "App\Controllers\PageController::masterCureTireCode");
$app->get("/master/warehouse", "App\Controllers\PageController::masterWarehouse");
$app->get("/master/location", "App\Controllers\PageController::masterLocation");
$app->get("/master/disposal", "App\Controllers\PageController::masterDisposal");
$app->get("/master/company", "App\Controllers\PageController::masterCompany");
$app->get("/master/department", "App\Controllers\PageController::masterDepartment");
$app->get("/master/user", "App\Controllers\PageController::user");
$app->get("/master/menu", "App\Controllers\PageController::masterMenu");
$app->get("/master/permission", "App\Controllers\PageController::masterPermission");
$app->get("/master/defect", "App\Controllers\PageController::masterDefect");
$app->get("/master/authorize", "App\Controllers\PageController::authorize");
$app->get("/master/employee", "App\Controllers\PageController::employee");
// $app->get("/master/gate", "App\Controllers\PageController::gate");
$app->get("/master/movement_type", "App\Controllers\PageController::movementType");
$app->get("/master/requsition_note", "App\Controllers\PageController::requsitionNote");
$app->get("/master/Return_Cause", "App\Controllers\PageController::Return_Cause");
$app->get("/master/pressmaster", "App\Controllers\PageController::masterPressCuring");
$app->get("/master/batchchangdate", "App\Controllers\PageController::changbatch");
$app->get("/master/checkgreentire", "App\Controllers\PageController::checkgreentire");

//send to warehouse
$app->get("/warehouse/sendtowarehouse", "App\Controllers\PageController::sendtowarehouse");
$app->get("/api/press/alltruck", "App\Controllers\BOIController::alltruck");
$app->get("/api/press/allround", "App\Controllers\BOIController::allround");
$app->post("/api/warehousesendtable/create", "App\Controllers\WarehouseController::createwhsendtable");
$app->get("/api/whsendwarehouse/all", "App\Controllers\WarehouseController::allwhtable");
//$app->get("/api/whsendwarehouse/allline", "App\Controllers\WarehouseController::alllinewhtable");
$app->get("/api/whsendwarehouse/([^/]+)/allline", "App\Controllers\WarehouseController::alllinewhtable");
$app->get("/master/round", "App\Controllers\PageController::round");
$app->post("/api/round/create", "App\Controllers\BOIController::createround");
$app->get("/mobile/finaltowh", "App\Controllers\PageHandheldController::finaltowh");
$app->get("/mobile/finaltowhcreate", "App\Controllers\PageHandheldController::finaltowhcreate");
$app->get("/api/press/alltmobiletruck", "App\Controllers\BOIController::alltmobiletruck");
$app->get("/sendwhround/print/([^/]+)", "App\Controllers\WarehouseController::printLinewhsendround");
$app->get("/report/finaltowh/report", "App\Controllers\ReportController::finaltowh");
$app->post("/report/finalsendwh/FisnlSendwhPdf", "App\Controllers\ReportController::FinalWhPdf");
$app->get("/api/invent/warehouse/total_finaltowh/([^/]+)", "App\Controllers\InventController::countFinalToWh");
$app->post("/api/truck/check", "App\Controllers\BOIController::truckcheck");

// Report
$app->get("/report/onhand", "App\Controllers\PageController::ReportOnhand");
$app->get("/report/greentire/hold", "App\Controllers\PageController::reportGreentireHold");
$app->get("/report/final/hold", "App\Controllers\PageController::reportFinalHold");
$app->get("/report/final/hold/excel/([^/]+)", "App\Controllers\ReportController::FinalHoldExcel");
$app->get("/report/gtcurefinal", "App\Controllers\PageController::reportGtCureFinal");
$app->get("/report/greentire/scrap", "App\Controllers\ReportController::greentireScrap");
$app->get("/report/greentire/repairwarhousereport", "App\Controllers\ReportController::repairwarhousereport");
$app->get("/report/greentire/repairgreentirereport", "App\Controllers\ReportController::repairgreentirereport");
$app->get("/report/greentire/repairfinalreport", "App\Controllers\ReportController::repairfinalreport");
$app->get("/report/curetire/scrap", "App\Controllers\ReportController::curetireScrap");
$app->get("/report/curetire/master", "App\Controllers\ReportController::curetireMaster");
$app->get("/report/curetire/master/pdf", "App\Controllers\ReportController::curetireMasterPdf");
$app->get("/report/curetire/master/excel", "App\Controllers\ReportController::curetireMasterExcel");
// $app->get("/report/greentire/scrap/([^/]+)/([^/]+)/([0-9]+)", "App\Controllers\ReportController::greentireScrapPdf");
//$app->get("/report/curetire/scrap/([^/]+)/([^/]+)/([0-9]+)", "App\Controllers\ReportController::curetireScrapPdf");
$app->post("/report/curetire/scrap_report", "App\Controllers\ReportController::curetireScrapPdf");
$app->get("/report/building_ax", "App\Controllers\ReportController::buildingAx");
$app->post("/report/building_ax/pdf", "App\Controllers\ReportController::buildingAxPdf");
$app->post("/report/greentire/greentireScrapPdf", "App\Controllers\ReportController::greentireScrapPdf");
$app->post("/report/greentire/greentireRepairPdf", "App\Controllers\ReportController::greentireRepairPdf");
$app->post("/report/greentire/greentireRepairFinalPdf", "App\Controllers\ReportController::greentireRepairFinalPdf");
$app->get("/report/curing_ax", "App\Controllers\ReportController::curingAx");
$app->post("/report/curing_ax/pdf", "App\Controllers\ReportController::curingAxPdf");
$app->get("/report/curing_ax_send", "App\Controllers\ReportController::curingAxSend");
$app->post("/report/curing_axsend/pdf", "App\Controllers\ReportController::curingAxSendPdf");
// $app->get("/report/curing", "App\Controllers\ReportController::curingReport");
// $app->post("/report/curing/pdf", "App\Controllers\ReportController::curingReportPdf");
$app->get('/report/greentire/hold_unhold_repair', 'App\Controllers\ReportController::renderGreentireHoldUnholdAndRepair');
$app->post('/report/greentire/hold_unhold_repair/pdf', 'App\Controllers\ReportController::GreentireHoldUnholdAndRepair');
$app->get('/report/final/hold_unhold_repair', 'App\Controllers\ReportController::renderFinalHoldUnholdAndRepair');
$app->post('/report/final/hold_unhold_repair/pdf', 'App\Controllers\ReportController::FinalHoldUnholdAndRepair');
$app->get("/report/building_acc", "App\Controllers\ReportController::buildingAcc");
$app->post("/report/building_acc/pdf", "App\Controllers\ReportController::buildingAccPdf");
$app->get("/report/greentire/scrapacc", "App\Controllers\ReportController::greentireScrapacc");
$app->post("/report/greentire/greentireScrapAccPdf", "App\Controllers\ReportController::greentireScrapAccPdf");

############### IMPORT #####################
$app->get("/import/topturn", "App\Controllers\ImportController::importTopTurn");
$app->get("/import/curecode", "App\Controllers\ImportController::importCureCode");


// ### nueng Report #####
$app->get("/report/shipdetail", "App\Controllers\PageController::ReportShipDetail");
$app->get("/api/press/loadid", "App\Controllers\PressController::loadid");
$app->get("/api/press/externorderkey", "App\Controllers\PressController::externorderkey");
$app->post("/api/pdf/shipdetail", "App\Controllers\ReportController::genshipdetailPDF");
$app->get("/report/building", "App\Controllers\PageController::ReportBuilding");
$app->get("/report/buildingt3", "App\Controllers\PageController::ReportBuildingt3");
$app->post("/api/pdf/building", "App\Controllers\ReportController::genbuildingPDF");
$app->post("/api/pdf/buildingt3", "App\Controllers\ReportController::genbuildingPDFt3");
$app->get("/report/internal", "App\Controllers\PageController::ReportInternal");
$app->post("/api/pdf/internal", "App\Controllers\ReportController::geninternalPDF");
$app->get("/report/curing", "App\Controllers\PageController::ReportCuring");
$app->get("/report/grentrecode", "App\Controllers\PageController::ReportGreentireCode");
$app->get("/report/buildingbycode", "App\Controllers\PageController::ReportBuildingCode");
$app->get("/api/press/allBDF", "App\Controllers\PressController::allBDF");
$app->get("/api/press/allBDFA", "App\Controllers\PressController::allBDFA");
$app->get("/api/press/allBOI", "App\Controllers\BOIController::allBOI");
$app->get("/api/press/allGT", "App\Controllers\BOIController::allGT");
$app->get("/api/curecode/curecodemaster", "App\Controllers\PressController::allCurecode");
$app->post("/api/pdf/curing", "App\Controllers\ReportController::gencuringPDF");
$app->get("/report/greentire/inventory", "App\Controllers\PageController::ReportGreentireInventory");
$app->post("/api/pdf/inventory", "App\Controllers\ReportController::geninventoryPDF");
$app->get("/report/warehouse/sent", "App\Controllers\PageController::ReportWarehousesent");
$app->get("/report/final/sent", "App\Controllers\PageController::ReportFinalsent");
$app->get("/report/greentire/inspection", "App\Controllers\PageController::ReportGreentireInspection");
$app->get("/api/press/allday", "App\Controllers\PressController::allday");
$app->get("/api/press/allnight", "App\Controllers\PressController::allnight");
// timenew
$app->get("/api/press/alldaynew", "App\Controllers\PressController::alldaynew");
$app->get("/api/press/allnightnew", "App\Controllers\PressController::allnightnew");

$app->post("/api/pdf/warehouse", "App\Controllers\ReportController::genwarehousePDF");
$app->post("/api/pdf/final", "App\Controllers\ReportController::genFinalPDF");
$app->post("/api/pdf/greentireinspection", "App\Controllers\ReportController::greentireInspectionReport");
$app->get("/report/warehouse/recive", "App\Controllers\PageController::ReportWarehouserecive");
$app->get("/api/brand/allbrand", "App\Controllers\ItemController::allbrand");
$app->get("/report/building_machine", "App\Controllers\ReportController::buildingMachine");
$app->post("/report/pdf/building-report-by-machine", "App\Controllers\ReportController::buildingMachinePdf");
$app->get("/report/curingpress", "App\Controllers\PageController::ReportCuringPress");
$app->get("/report/loading/Containertire", "App\Controllers\PageController::ReportContainertire");
$app->post("/api/pdf/loadtireount", "App\Controllers\ReportController::loadtirePDF");
$app->get("/report/warehouse/Warehouseonhand", "App\Controllers\PageController::ReportWarehouseonhand");
$app->post("/api/pdf/warehouseonhand", "App\Controllers\ReportController::warehousrOnhandPDF");
$app->post("/api/pdf/greentirecode", "App\Controllers\ReportController::greentirecodepdf");
$app->post("/api/pdf/buildingcode", "App\Controllers\ReportController::buildingcodepdf");

$app->post("/api/pdf/curingpress", "App\Controllers\ReportController::curingPress");
// J
$app->get("/report/wipfinalfg", "App\Controllers\PageController::ReportWIPFinalFG");
$app->post("/api/pdf/wipfinalfg", "App\Controllers\ReportController::genwipfinalfgPDF");
$app->get("/report/cure/inventory", "App\Controllers\PageController::ReportCureInventory");
$app->post("/api/pdf/cureinventory", "App\Controllers\ReportController::gencureinventoryPDF");
$app->get("/api/loading/report/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\ReportController::LoadingPDF");

$app->get("/api/movementissue/report/([^/]+)/([^/]+)", "App\Controllers\ReportController::MovementIssueExcel");
// ######################################################
// Saba
$app->get("/report/lightbuff/inventory", "App\Controllers\PageController::ReportLightBuffInventory");
$app->get("/report/lightbuff/report", "App\Controllers\PageController::ReportLightBuff");
$app->post("/api/pdf/lightbuffinventory", "App\Controllers\ReportController::genlightbuffinventoryPDF");
$app->post("/api/pdf/Lightbuffreport", "App\Controllers\ReportController::BuffreportServiceallpdf");


// Auth
$app->get("/d/auth", "App\Controllers\PageController::desktopLogin");
$app->get("/hh/auth", "App\Controllers\PageHandheldController::handheldLogin");
$app->get("/user/logout", "App\Controllers\UserController::logout");
$app->post("/clearsession", "App\Controllers\UserController::clearSession");
$app->get("/user/logoutmobile", "App\Controllers\UserController::logoutmobile");

// Service API
/* GET */
$app->get("/api/barcode/printing/last", "App\Controllers\BarcodeController::getLastNumber");
$app->get("/api/department/all", "App\Controllers\DepartmentController::all");
$app->get("/api/warehouse/all", "App\Controllers\WarehouseController::all");
$app->get("/api/location/all", "App\Controllers\LocationController::all");
$app->get("/api/company/all", "App\Controllers\CompanyController::all");
$app->get("/api/item/all", "App\Controllers\ItemController::all");
$app->get("/api/permission/all", "App\Controllers\PermissionController::all");

// Employee
$app->get("/api/employee/all", "App\Controllers\EmployeeController::all");
$app->get("/api/employee/all/department/([^/]+)", "App\Controllers\EmployeeController::allByDepartmentName");
$app->get("/api/employee/all/by_status", "App\Controllers\EmployeeController::allByStatus");
$app->get("/api/employee/([^/]+)/division", "App\Controllers\EmployeeController::getDivisionByEmpCode");

$app->get("/api/press/all", "App\Controllers\PressController::all");
$app->get("/api/press_arm/all", "App\Controllers\PressArmController::all");
$app->get("/api/user/all", "App\Controllers\UserController::all");
$app->get("/api/template/all", "App\Controllers\TemplateController::all");
$app->get("/api/FIFOBatch/all", "App\Controllers\FIFOBatchController::all");
$app->get("/api/template/last", "App\Controllers\TemplateController::getLastRec");
$app->get("/api/template/generate/([^/]+)/([^/]+)", "App\Controllers\TemplateController::generate");
$app->get("/api/menu/all", "App\Controllers\MenuController::all");
$app->get("/api/defect/all", "App\Controllers\DefectController::all");
$app->get("/api/defect/reverse", "App\Controllers\DefectController::reverse");
$app->get("/api/defect/master/all", "App\Controllers\DefectController::masterAll");
$app->get("/api/greentire/all", "App\Controllers\GreentireController::all");
$app->get("/api/building/all", "App\Controllers\BuildingController::all");
$app->get("/api/boi/all", "App\Controllers\BOIController::all");
$app->get("/api/building/boi/all", "App\Controllers\BuildingController::boi");
$app->get("/api/curetire/all", "App\Controllers\CureTireController::all");
$app->get("/api/disposal/all", "App\Controllers\DisposalController::all");
$app->get("/api/disposal/action/all", "App\Controllers\DisposalController::actionAll");
$app->get("/api/disposal/company/all", "App\Controllers\DisposalController::companyAll");
$app->get("/api/mold/all", "App\Controllers\MoldController::all");
$app->get("/api/onhand/all", "App\Controllers\OnhandController::all");
$app->get("/api/invent/table/all", "App\Controllers\InventController::allInventTable");
$app->get("/api/Device/table/all", "App\Controllers\DeviceController::allInventTable");
$app->get("/api/invent/trans/([^/]+)", "App\Controllers\InventController::transDetail");
$app->get("/api/Device/trans/([^/]+)", "App\Controllers\DeviceController::transDetail");
$app->get("/api/Device/device/all", "App\Controllers\DeviceController::deviceall");
$app->get("/api/Device/user/all", "App\Controllers\DeviceController::userall");
$app->get("/api/Device/vendor/all", "App\Controllers\DeviceController::vedorall");
$app->get("/api/menu/generate", "App\Controllers\MenuController::generate");
$app->get("/api/menu/generateMobile", "App\Controllers\MenuController::generateMobile");
$app->get("/api/location/by_warehouse/([^/]+)", "App\Controllers\LocationController::getLocationByWarehouse");
$app->get("/api/shift/all", "App\Controllers\ShiftController::getAll");
$app->get("/api/warehouse_type/all", "App\Controllers\WarehouseController::getAllWarehouseType");
$app->get("/api/authorize/all", "App\Controllers\AuthorizeController::all");
$app->get("/api/gate/all", "App\Controllers\GateController::all");
$app->get("/api/movement_type/all", "App\Controllers\MovementController::allMovementType");
$app->get("/api/movement_type/Requisitionlist/([^/]+)", "App\Controllers\MovementController::Requisitionlist");
$app->get("/api/movement_issue/all", "App\Controllers\MovementController::allMovementIssue");
$app->get("/api/movement_issue/([^/]+)/latest", "App\Controllers\MovementController::getLatestJournalTransByJournalId");
$app->get("/api/movement_issue/([^/]+)/item", "App\Controllers\MovementController::getInventJournalTable");
$app->post("/api/movement_issue/EditreacordLine/([^/]+)/([^/]+)/([^/]+)", "App\Controllers\MovementController::EditreacordLine");
$app->post("/api/movement_issue/checkSN/([^/]+)/([^/]+)", "App\Controllers\MovementController::checkSN");
$app->post("/api/movement_issue/checkStatusPostBack/([^/]+)", "App\Controllers\MovementController::checkStatusPostBack");
$app->post("/api/movement_issue/UpdatePostBack/([^/]+)", "App\Controllers\MovementController::UpdatePostBack");
$app->post("/api/movement_issue/checkAuthorizePostBack", "App\Controllers\MovementController::checkAuthorizePostBack");

$app->get("/movement/Mobile", "App\Controllers\PageController::movementmobile");
$app->get("/api/movement_issue/item", "App\Controllers\MovementController::ItemMaster");
$app->get('/api/v1/movement/item/available/([^/]+)', 'App\Movement\MovementController::getItemAvailable');
$app->get("/api/movement_issue/Batch", "App\Controllers\MovementController::Batchmaster");
$app->get('/api/v1/movement/batch/available/([^/]+)/([^/]+)', 'App\Movement\MovementController::getBatchAvailable');
$app->post('/api/v1/movement/batch/checkcounbatch/([^/]+)/([^/]+)/([^/]+)/([^/]+)', 'App\Movement\MovementController::checkcounbatch');

$app->get("/api/returncause_issue/all", "App\Controllers\ReturnCauseController::allreturncause");
$app->get("/api/returncause_note/all/([^/]+)", "App\Controllers\ReturnCauseController::allcheck");
$app->get("/api/requsition_note/all", "App\Controllers\RequsitionController::all");
$app->get("/api/ReturnCause/all", "App\Controllers\ReturnCauseController::all");
$app->get("/api/ReturnCause/([^/]+)/latest", "App\Controllers\ReturnCauseController::getLatestJournalTransByJournalId");
$app->get("/api/ReturnCause/Type", "App\Controllers\ReturnCauseController::allReturnType");

$app->get("/api/report/greentire/hold", "App\Controllers\OnhandController::getGreentireHold");
$app->get("/api/report/final/hold/([^/]+)", "App\Controllers\OnhandController::getFinalHold");
$app->get("/api/report/gtcurefinal/([^/]+)/([^/]+)", "App\Controllers\OnhandController::getGtCureFinal");
$app->get('/api/loading/table/all_status', 'App\Controllers\LoadingController::getLoadingTableAllStatus');
$app->get('/api/authorize/field', 'App\Controllers\AuthorizeController::getPermissionField');
$app->get('/api/loading/line/([^/]+)', 'App\Controllers\LoadingController::getLoadingLine');

// $app->get('/api/loading/table/([^/]+)', 'App\Controllers\LoadingController::getLoadingTable');
$app->get('/api/loading/table/all', 'App\Controllers\LoadingController::getLoadingTableAll');
$app->get('/api/loading/table/([^/]+)/create', 'App\Controllers\LoadingController::createLoadingTable'); // Loading on mobile
$app->get('/api/barcode/([^/]+)', 'App\Controllers\BarcodeController::getBarcodeInfo');
$app->get("/api/loading/trans/([^/]+)/([^/]+)/([^/]+)", 'App\Controllers\LoadingController::loadingTrans');
$app->get('/api/loading/pickinglist_by_orderid/([^/]+)', 'App\Controllers\LoadingController::getPickingListByOrderId');
$app->get('/api/actions', 'App\Controllers\PermissionController::actionsAll');
$app->get('/api/actions/user', 'App\Controllers\PermissionController::actionsUser');
$app->get('/api/actions/user/menu/([^/]+)', 'App\Controllers\PermissionController::actionsUserByPermissionDesktop');
$app->get('/api/actions/user/active/([^/]+)', 'App\Controllers\PermissionController::userActionActive');
$app->get("/api/invent/warehouse/total_receive/([^/]+)", "App\Controllers\InventController::countReceiveToWarehouseFromFinal");
$app->get("/api/user/warehouse", "App\Controllers\WarehouseController::getUserWarehouse");

// ta
$app->post("/api/quality/check", "App\Controllers\WarehouseController::qualitycheck");
$app->get("/quality", "App\Controllers\PageController::quality");
$app->get("/report/quality/report", "App\Controllers\PageController::reportquality");
$app->post("/api/excel/quality", "App\Controllers\ReportController::qualityexcel");
$app->get("/report/Finalfinishing/report", "App\Controllers\PageController::reportFinalfinishing");
$app->post("/report/final_ins/pdf", "App\Controllers\ReportController::finalreportins");
$app->get("/report/repair/inventory", "App\Controllers\PageController::ReportRepairInventory");
$app->post("/api/pdf/repairinventory", "App\Controllers\ReportController::repairinventory");
// chang batchweek
$app->get("/api/batch/changall", "App\Controllers\CureTireController::batchchang");
$app->post("/api/batch/updatebatch", "App\Controllers\CureTireController::updatebatch");
// saba
$app->post("/api/checkgreentire/update", "App\Controllers\GreentireController::updatecheckgreentire");

$app->get("/report/dailyrepair/report", "App\Controllers\PageController::reportdailyrepair");
$app->post("/report/dailyrepair/pdf", "App\Controllers\ReportController::dailyrepair");
$app->get("/api/checkgreentire/all", "App\Controllers\GreentireController::checkgreentireall");


/* POST */


$app->post("/api/barcode/printing", "App\Controllers\BarcodeController::printing");
$app->post("/api/building/check", "App\Controllers\BuildingController::check");
$app->post("/api/greentire/barcode/check", "App\Controllers\GreentireController::checkBarcode");
$app->post("/api/user/create", "App\Controllers\UserController::create");
$app->post("/api/user/handheld/auth", "App\Controllers\UserController::handheldAuth");
$app->post("/api/curing/save", "App\Controllers\CuringController::curing");
$app->post("/api/curing/chkFIFO/([^/]+)/([^/]+)", "App\Controllers\CuringController::ChkFIFOBatch");
$app->post("/api/xray/issue/wh", "App\Controllers\XrayController::issueToWH");
$app->post("/api/warehouse/create", "App\Controllers\WarehouseController::create");
$app->post("/api/menu/create", "App\Controllers\MenuController::create");
$app->post("/api/company/create", "App\Controllers\CompanyController::create");
$app->post("/api/defect/create", "App\Controllers\DefectController::create");
$app->post("/api/defect/update", "App\Controllers\DefectController::update");
$app->post("/api/department/create", "App\Controllers\DepartmentController::create");
$app->post("/api/greentire/create", "App\Controllers\GreentireController::create");
$app->post("/api/building/create", "App\Controllers\BuildingController::create");
$app->post("/api/boi/create", "App\Controllers\BOIController::create");
$app->post("/api/press/create", "App\Controllers\PressController::create");
$app->post("/api/permission/create", "App\Controllers\PermissionController::create");
$app->post("/api/search/barcode", "App\Controllers\TrackingController::searchByBarcode");
$app->post("/api/search/beforelast/([^/]+)", "App\Controllers\TrackingController::searchBybeforelast");
$app->post("/api/search/beforelastscrap/([^/]+)", "App\Controllers\TrackingController::searchBybeforelastDeafceScrap");
$app->post("/api/search/beforelastHold/([^/]+)", "App\Controllers\TrackingController::searchBybeforelastDeafce");
$app->post("/api/press/createcuring", "App\Controllers\PressController::createcuring");

$app->post("/api/search/barcode2", "App\Controllers\TrackingController::searchByBarcode2");
$app->post("/api/search/svopcr", "App\Controllers\TrackingController::searchByBarcodeSvoPCR");
$app->post("/api/search/barcode/line", "App\Controllers\TrackingController::searchByBarcodeLine");
$app->post("/api/search/hold", "App\Controllers\HoldController::checkHold");
$app->post("/api/search/repair", "App\Controllers\RepairController::checkRepair");
$app->post("/api/location/create", "App\Controllers\LocationController::create");
$app->post("/api/curetire/create", "App\Controllers\CureTireController::create");
$app->post("/api/disposal/create", "App\Controllers\DisposalController::create");
$app->post("/api/user/desktop/auth", "App\Controllers\UserController::desktopAuth");
$app->post("/api/mold/create", "App\Controllers\MoldController::create");
$app->post("/api/greentire/receive", "App\Controllers\GreentireController::receive");
$app->post("/api/greentire/receive_old", "App\Controllers\GreentireController::receiveOld");
$app->post("/api/greentire/delete", "App\Controllers\GreentireController::delete");
$app->post("/api/building/delete", "App\Controllers\BuildingController::delete");
$app->post("/api/press/delete", "App\Controllers\PressController::delete");
$app->post("/api/warehouse/incoming", "App\Controllers\WarehouseController::incoming");
$app->post("/api/hold", "App\Controllers\HoldController::hold");
$app->post("/api/beforehold", "App\Controllers\HoldController::beforehold");

$app->post("/api/repair", "App\Controllers\RepairController::repair");
$app->post("/api/repair_income/save", "App\Controllers\RepairIncomeController::save");
$app->post("/api/repair_outcome/save", "App\Controllers\RepairOutcomeController::save");
$app->post("/api/scrap", "App\Controllers\ScrapController::scrap");
$app->post("/api/unhold/authorize", "App\Controllers\HoldController::authorize");
$app->post("/api/unrepair/authorize", "App\Controllers\RepairController::authorize");
$app->post("/api/unhold", "App\Controllers\HoldController::unhold");
$app->post("/api/unrepair", "App\Controllers\RepairController::unrepair");
$app->post("/api/warehouse_type/create", "App\Controllers\WarehouseController::createType");
$app->post("/api/warehouse_type/delete", "App\Controllers\WarehouseController::deleteType");
$app->post("/api/authorize/create", "App\Controllers\AuthorizeController::create");
$app->post("/api/authorize/([0-9]+)/edit", "App\Controllers\AuthorizeController::edit");
$app->post("/api/employee/status/save", "App\Controllers\EmployeeController::setStatus");
$app->post("/api/gate/save", "App\Controllers\GateController::save");
$app->post("/api/final/save", "App\Controllers\FinalController::save");
$app->post("/api/grrentire/save", "App\Controllers\GreentireController::save");
$app->post("/api/movement_type/save", "App\Controllers\MovementController::save");
$app->post("/api/journal/table/save", "App\Controllers\MovementController::saveJournalTable");
$app->post("/api/journal/table/save1/([^/]+)", "App\Controllers\ReturnCauseController::saveJournalTable");
$app->post("/api/search/Withdrawal", "App\Controllers\MovementController::checkWithdrawal");
$app->post("/api/Withdrawal/save", "App\Controllers\MovementController::SaveWithdrawal");
$app->post("/api/journal/table/saveDestop", "App\Controllers\MovementController::saveJournalTableDestop");
$app->post("/api/journal/table/saveInventJournalLine", "App\Controllers\MovementController::saveInventJournalLine");
$app->post("/api/Device/table/saveDeviceTable", "App\Controllers\DeviceController::saveDeviceTable");
$app->post("/api/Device/table/saveDeviceTabletrans", "App\Controllers\DeviceController::saveDeviceTabletrans");
$app->post("/api/lightbuff/save", "App\Controllers\HoldController::lightbuff");

$app->post("/api/ReturnCause/issue/save", "App\Controllers\ReturnCauseController::savereturntIssue");
$app->post("/api/movement/issue/save", "App\Controllers\MovementController::saveMovementIssue");
$app->post("/api/requsition_note/save", "App\Controllers\RequsitionController::saveRequsitionNote");
$app->post("/api/requsition_note/save1", "App\Controllers\ReturnCauseController::saveRequsitionNote");
$app->post("/api/user/authorize", "App\Controllers\UserController::authorize");
$app->post('/api/movement/reverse/ok/save', "App\Controllers\MovementController::saveReverseOK");
$app->post('/api/movement/reverse/scrap/save', "App\Controllers\MovementController::saveReverseScrap");
$app->post("/api/movement_issue/complete", "App\Controllers\MovementController::completeIssue");
$app->post('/api/final/return/save', 'App\Controllers\FinalController::saveReturn');
$app->post('/api/authorize/type', 'App\Controllers\UserController::getAuthorizeType');
$app->post('/api/user/location', 'App\Controllers\UserController::getUserLocation');
$app->post('/api/location/([0-9]+)/edit', 'App\Controllers\LocationController::setLocation');
$app->post('/api/loading/pick/save', 'App\Controllers\LoadingController::savePick');
$app->post('/api/loading/unpick/save', 'App\Controllers\LoadingController::saveUnpick');
$app->post('/api/loading/is_custome_remainder', 'App\Controllers\LoadingController::isCustomRemainder');
$app->post('/api/loading/confirm', 'App\Controllers\LoadingController::confirm');
$app->post('/api/loading/cancel', 'App\Controllers\LoadingController::cancel');
$app->post('/api/loading/add_remainder', 'App\Controllers\LoadingController::addRemainder');
$app->post('/api/loading/force_confirm', 'App\Controllers\LoadingController::forceConfirm');
$app->post('/api/loading/pickinglist_ref', 'App\Controllers\LoadingController::savePickingListRef');
$app->post('/api/actions/edit', 'App\Controllers\PermissionController::actionsEdit');
$app->post('/api/actions/create', 'App\Controllers\PermissionController::actionsCraete');
$app->post('/api/v1/adjust', 'App\Controllers\AdjustController::store');
$app->post("/api/returncomplete_issue/complete", "App\Controllers\ReturnCauseController::completeIssue");
// $app->post("/api/v1/item/sync","App\Controllers\ItemController::syncItem");
$app->post("/api/movement/item/sync", "App\Controllers\MovementController::syncItem");

// Import
$app->post("/api/import/topturn", "App\Controllers\ImportController::saveImportTopturn");
$app->post("/api/import/curecode", "App\Controllers\ImportController::saveImportCureCode");

//mobile
$app->get("/xray/issue/wh_mb", "App\Controllers\PageController::xrayIssueWH_mb");
$app->get("/final/incoming_mb", "App\Controllers\PageController::finalIncoming_mb");
$app->get('/movement/reverse_mb', "App\Controllers\PageController::movementReverse_mb");
$app->get("/greentire/incomingcheck_Mb", "App\Controllers\PageController::GreentireCheckQc_Mb");
$app->get("/hold_Mb", "App\Controllers\PageController::hold_Mb");
$app->get("/repair_Mb", "App\Controllers\PageController::repair_Mb");
$app->get("/greentire/scrap_Mb", "App\Controllers\PageController::scarp_Mb");
$app->get("/unhold_Mb", "App\Controllers\PageController::unhold_Mb");
$app->get("/curing_Mb", "App\Controllers\PageHandheldController::curingHandheld_mb");
$app->get("/curing_no_serial_Mb", "App\Controllers\PageHandheldController::curingHandheldWithoutSerial_mb");
$app->get('/movement/reverse_Mb', "App\Controllers\PageController::movementReverse_mb");
$app->get("/final/incoming_Mb", "App\Controllers\PageController::finalIncoming_mb");
$app->get("/movement/Mobile_Mb", "App\Controllers\PageController::movementmobile_mb");
$app->get("/page_mobile/handheld_login", "App\Controllers\PageController::handheldindex");
$app->get("/tracking_mb", "App\Controllers\PageController::tracking_mobile");
$app->get("/mobile/finaltowhcreate_mb", "App\Controllers\PageHandheldController::finaltowhcreate_mb");
$app->get("/mobile/finaltowh_mb", "App\Controllers\PageHandheldController::finaltowh_mb");
$app->get('/final_return_mb', 'App\Controllers\PageController::finalReturn_mb');
$app->get("/quality_mb", "App\Controllers\PageController::quality_mobile");
$app->get("/api/warehouse/total_quality/([^/]+)", "App\Controllers\WarehouseController::countquality");
$app->get("/lightbuff_Mb", "App\Controllers\PageController::lightbuff_Mb");
$app->get("/repair_income_Mb", "App\Controllers\PageController::repair_income_Mb");
$app->get("/repair_outcome_Mb", "App\Controllers\PageController::repair_outcome_Mb");





// V1
require_once "./routes/profile.php";
require_once "./routes/itemset.php";
require_once "./routes/bom.php";
require_once "./routes/unbom.php";
require_once "./routes/item.php";
require_once "./routes/authorize.php";
require_once "./routes/foil.php";
require_once './routes/check.php';
require_once './routes/build.php';
require_once './routes/barcode.php';
require_once './routes/serial.php';
require_once './routes/finish_good.php';
require_once './routes/batch.php';
require_once './routes/employee.php';
require_once './routes/defect.php';
require_once './routes/phase2.php';
require_once './routes/component.php';
require_once "./routes/repair.php";


// v2
require_once './app/v2/Pallet/PalletRoute.php';
require_once './app/v2/Location/LocationRoute.php';
require_once './app/v2/Item/ItemRoute.php';
require_once './app/v2/Batch/BatchRoute.php';
require_once './app/v2/PDF/PDFRoute.php';
require_once './app/v2/Component/ComponentRoute.php';
require_once './app/v2/Report/ReportRoute.php';
require_once './app/v2/Loading/LoadingRoute.php';
require_once './app/v2/Transfer/TransferRoute.php';
require_once './app/v2/Rate/RateRoute.php';


// v3
require_once './app/WarehouseLocation/WarehouseLocationRoute.php';
require_once './app/ScrapComponent/ScrapComponentRoute.php';
require_once './app/Logs/LogsRoute.php';
require_once './app/WarehouseCounting/WarehouseCountingRoute.php';

$app->run();
