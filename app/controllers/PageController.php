<?php

namespace App\Controllers;

use App\Components\Security;
use App\Components\Authentication;
use App\Components\Utils;
use App\Controllers\UserController;

class PageController
{
	private $auth = null;
	private $secure = null;
	private $utils = null;
	private $user = null;

	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
		$this->utils = new Utils;
		$this->user = new UserController;

		if ($this->auth->isLogin() === false) {
			renderView('page/login');
			exit;
		}

		$this->errorText = $this->user->getDefaultPage();
	}

	public function index()
	{
		if ($this->auth->isLogin() === false) {
			renderView("page/login");
		} else {
			if ($this->secure->isAccess() === false) {
				exit(renderView('page/404'));
			} else {
				renderView("page/home");
			}
		}
	}

	public function checkdevicepage()
	{

		renderView("page/checkdevice");
	}


	public function welcome()
	{
		$detect = new \Mobile_Detect;

		if ($detect->isMobile()) {
			header("Location:" . $this->user->getDefaultLink());
		} else {
			header("Location:" . APP_ROOT . '/home');
		}
	}

	public function desktopLogin()
	{
		renderView("page/login");
	}

	public function tracking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/tracking");
	}

	public function masterWarehouse()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse");
	}

	public function masterCureTireCode()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/curetire");
	}

	public function masterLocation()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/location");
	}

	public function masterDisposal()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/disposal");
	}

	public function masterMenu()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/menu");
	}

	public function masterPermission()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/permission");
	}

	public function masterDepartment()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/department");
	}

	public function barcodePrinting()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/barcode_printing");
	}

	public function barcodeCuring()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/barcode_curing");
	}

	public function templateRegister()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/template_register");
	}

	public function masterCompany()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/company");
	}

	public function user()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/user");
	}

	public function greentireIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_incoming");
	}

	public function greentireIncomingOld()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_incoming_old");
	}

	public function xray()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/xray");
	}

	public function loadingDesktop()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/loading_desktop");
	}

	public function loadingMobile()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/loading_mobile");
	}

	public function stockTaking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/stocktaking");
	}

	public function masterDefect()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/defect");
	}

	public function masterGreentireCode()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_code");
	}

	public function masterBuilding()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/building");
	}

	public function masterPress()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/press");
	}

	public function masterPressCuring()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/presscuring");
	}

	public function hold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/hold");
	}

	public function repair()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/repair");
	}

	public function scarp()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/scarp");
	}

	public function masterMold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/mold");
	}

	public function ReportOnhand()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("pagemaster/report_onhand");
	}

	public function xrayIssueWH()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/xray_issue_wh");
	}

	//mobile
	public function xrayIssueWH_mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/xray_issue_wh_mb");
	}

	public function warehouseIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse_incoming");
	}

	public function unhold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/unhold");
	}

	public function unrepair()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/unrepair");
	}

	public function movementIssue()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_issue");
	}
	public function fifobatch()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/fifo_batch");
	}

	public function landing()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			renderView("page/landing");
		} else {
			renderView("page/home");
		}
	}

	public function warehouseType()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/warehouse_type");
	}

	public function authorize()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/authorize");
	}

	public function employee()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/employee");
	}

	public function finalIncoming()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_incoming");
	}
	//mobile
	public function finalIncoming_mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_incoming_mb");
	}

	public function movementType()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_type");
	}

	public function requsitionNote()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/requsition_note");
	}

	public function movementIssueNew()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_issue_new");
	}

	public function movementReverse()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_reverse");
	}
	//mobile
	public function movementReverse_mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/movement_reverse_mb");
	}
	public function finalReturn_mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_return_mb");
	}

	public function finalReturn()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/final_return");
	}

	public function actions()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/actions");
	}

	public function reportGreentireHold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/report_greentire_hold");
	}

	public function reportFinalHold()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/report_final_hold");
	}

	public function adjust()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('page/adjust');
	}

	public function ReportBuilding()
	{
		renderView("pagemaster/building");
	}

	public function ReportInternal()
	{
		renderView("pagemaster/internal");
	}

	public function ReportCuring()
	{
		renderView("pagemaster/curing");
	}

	public function ReportShipDetail()
	{
		renderView("pagemaster/shipdetail");
	}

	public function ReportGreentireInventory()
	{
		renderView("pagemaster/greentireinventory");
	}

	public function ReportWarehousesent()
	{
		renderView("pagemaster/warehousesent");
	}

	public function ReportWarehouserecive()
	{
		renderView("pagemaster/warehouserecive");
	}

	public function ReportCuringPress()
	{
		renderView("pagemaster/curingpress");
	}
	// J Report
	public function ReportCureInventory()
	{
		renderView("pagemaster/cureinventory");
	}

	public function ReportWIPFinalFG()
	{
		renderView("pagemaster/wipfinal");
	}

	public function tracking_v2()
	{
		renderView("page/tracking_v2");
	}
	//mobile
	public function tracking_mobile()
	{
		renderView("page/tracking_mobile");
	}

	public function tracking_v3()
	{
		renderView("page/tracking_v3");
	}

	public function logLogin()
	{
		renderView("page/logLogin");
	}

	public function movementmobile()
	{
		renderView("page/movementmobile");
	}

	public function Return_Cause()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/Return_Cause");
	}

	public function re_cause()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/re_cause");
	}

	public function issue_returncause()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/issue_returncause");
	}
	public function add_more_user()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			renderView("page/add_more_user");
		} else {
			renderView("page/add_more_user");
		}
	}
	public function Deduct_Rate()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			renderView("page/deduct_rate");
		} else {
			renderView("page/deduct_rate");
		}
	}

	public function boi()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/BOI");
	}



	// public function DetailReturncause()
	// {
	// 	if ($this->secure->isAccess() === false) {
	// 		exit(renderView('page/404'));
	// 	}
	// 	renderView("page/DetailReturncause");
	// }
	public function ReportRateBuilding()
	{
		renderView("pagemaster/rate_build");
	}
	public function ReportFinalsent()
	{
		renderView("pagemaster/finalsend");
	}

	public function ReportActBuilding()
	{
		renderView("pagemaster/act_build");
	}

	public function BuildSchedule()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/rate_buildsch");
	}

	public function Rate_Master()
	{
		// renderView("page/ratemaster");
		renderView("page/rate_master");
	}
	public function GreentireCheckQc()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/greentire_checkqc");
	}
	public function ReportGreentireInspection()
	{
		renderView("pagemaster/GreentireInspection");
	}


	public function ReportDeduct()
	{

		renderView("pagemaster/report_deduct");
	}

	public function ReportRateMonthly()
	{

		renderView("pagemaster/report_rate_month");
	}

	public function ReportRateCuring()
	{
		renderView("pagemaster/rate_cure");
	}

	public function ReportLogBuilding()
	{
		renderView("pagemaster/report_logbuild");
	}

	public function reportGtCureFinal()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/report_gt_cure_final");
	}

	public function ReportBuildingt3()
	{
		renderView("pagemaster/buildingt3");
	}

	public function ScrapCheck()
	{
		renderView("page/scrap_check");
	}

	public function ReportContainertire()
	{
		renderView("pagemaster/loadtirereport");
	}

	public function ReportWarehouseonhand()
	{
		renderView("pagemaster/WarehouseInventoryReport");
	}
	public function ReportGreentireCode()
	{
		renderView("pagemaster/ReportGreentireCode");
		//renderView("pagemaster/test");
	}

	public function ReportBuildingCode()
	{
		renderView("pagemaster/ReportBuildingCode");
		//renderView("pagemaster/test");
	}

	public function CureSchedule()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/cure_sch");
	}

	public function sendtowarehouse()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/sendtowarehouse");
	}

	public function round()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/round");
	}

	public function GreentireCheckQc_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/greentire_checkqc_Mb");
	}

	public function hold_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/hold_Mb");
	}

	public function repair_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/repair_Mb");
	}

	public function repair_income_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/repair_income_Mb");
	}

	public function repair_outcome_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/repair_outcome_Mb");
	}

	public function scarp_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/scarp_Mb");
	}

	public function unhold_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/unhold_Mb");
	}

	// public function movementReverse_mb()
	// {
	// 	if ($this->secure->isAccess() === false) {
	// 		exit(renderView('page/404'));
	// 	}
	// 	renderView("page_mobile/movement_reverse_Mb");
	// }

	// public function finalIncoming_mb()
	// {
	// 	if ($this->secure->isAccess() === false) {
	// 		exit(renderView('page/404'));
	// 	}
	// 	renderView("page_mobile/final_incoming_Mb");
	// }

	public function movementmobile_mb()
	{
		renderView("page_mobile/movementmobile_Mb");
	}

	public function handheldindex()
	{
		renderView("page_mobile/handheldindex");
	}

	public function quality()
	{
		renderView("page/quality");
	}
	//mobile
	public function quality_mobile()
	{
		renderView("page/quality_mobile");
	}
	//Report quality
	public function reportquality()
	{
		renderView("page/quality_checking");
	}
	//Final INS Report
	public function reportFinalfinishing()
	{
		renderView("page/final_finishing");
	}
	public function ReportRepairInventory()
	{
		renderView("pagemaster/repair_inventory");
	}
	public function reportdailyrepair()
	{
		renderView("page/daily_repair");
	}
	public function changbatch()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/batchchang");
	}
	public function lightbuff()
	{
		renderView("page/lightbuff");
	}

	public function lightbuff_Mb()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page_mobile/lightbuff_mb");
	}
	public function ReportLightBuffInventory()
	{
		renderView("pagemaster/lightbuffinventory");
	}
	public function ReportLightBuff()
	{
		renderView("pagemaster/lightbuffreport");
	}

	public function checkgreentire()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagemaster/checkgreentire");
	}

	public function svo_pcr_tracking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("page/svo_pcr_tracking");
	}
}
