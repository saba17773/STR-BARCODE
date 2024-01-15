<?php

namespace App\Controllers;

use App\Services\ReportService;
use App\Models\InventTrans;
use App\Components\Security;
use App\Components\Authentication;
use App\Services\WarehouseService;
use App\Services\FinalService;
use App\Services\BOIService;
use App\Services\GreentireService;
use App\Services\OnhandService;


class ReportController
{
	private $secure = null;
	private $report = null;
	private $auth = null;
	private $boi = null;
	private $Greentire = null;
	public function __construct()
	{
		$this->secure = new Security;
		$this->report = new ReportService;
		$this->auth = new Authentication;
		$this->boi = new BOIService;
		$this->Greentire = new GreentireService;

		if ($this->auth->isLogin() === false) {
			renderView('page/login');
			exit;
		}
	}

	public function greentireScrap()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_greentire_scrap");
	}

	public function curetireScrap()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_curetire_scrap");
	}

	public function greentireScrapacc()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_greentire_scrapAcc");
	}

	public function repairgreentirereport()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_greentire_repair");
	}

	public function repairfinalreport()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_greentire_repair_final");
	}




	// public function repairDaily()
	// {
	// 	if ($this->secure->isAccess() === false) {
	// 		exit(renderView('page/404'));
	// 	}

	// 	renderView("page/report_repair_daily");
	// }

	public function greentireScrapPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		//	print_r($_REQUEST); exit();
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		try {
			$data = $this->report->greentireScrap($date, $product_group, $pressBOI);

			if (count($data) === 0) {
				echo "No Data!";
				//	var_dump($data);
			} else {
				//var_dump($data);
				if ($check == 1) {
					renderView("page/report_greentire_scrap_pdf", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				} else {
					renderView("page/report_curetire_scrap_xcell", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}



		// var_dump($data); exit;


	}

	public function curetireScrapPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "checkdata");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		//print_r($_REQUEST); exit();
		$data = $this->report->curetireScrap($date, $product_group, $pressBOI);
		if ($check == 1) {
			renderView("page/report_curetire_scrap_pdf", [
				"data" => $data,
				"date" => $date,
				"BOIName" => $dataBOIName
			]);
		}
		if ($check == 2) {
			renderView("page/report_curetire_scrap_xcell_maser", [
				"data" => $data,
				"date" => $date,
				"BOIName" => $dataBOIName
			]);
		}
	}

	public function curingReport()
	{
		renderView("report/curing");
	}

	public function curingReportPdf()
	{
		$date = filter_input(INPUT_POST, "date");
		$shift = filter_input(INPUT_POST, "shift");
		$switch = filter_input(INPUT_POST, "switch");

		$getData = $this->report->curingReportPdf($date);

		$q2_array = [];

		$all_tire = [];
		foreach ($getData as $value) {
			$all_tire[] = $value;
		}

		$data = [];

		for ($i = 0; $i < count($all_tire); $i++) {

			if ($all_tire[$i]["Q1"] !== "" && $all_tire[$i]["Q1"] !== null) {
				$all_tire[$i]["Q1"] = trim($all_tire[$i]["Q1"] . "," . $all_tire[$i - 1]["Q1"], ",");
			} else {
				$all_tire[$i]["Q1"] = "";
			}

			if ($all_tire[$i]["Q2"] !== "" && $all_tire[$i]["Q2"] !== null) {
				$all_tire[$i]["Q2"] = trim($all_tire[$i]["Q2"] . "," . $all_tire[$i - 1]["Q2"], ",");
			} else {
				$all_tire[$i]["Q2"] = "";
			}

			if ($all_tire[$i]["Q3"] !== "" && $all_tire[$i]["Q3"] !== null) {
				$all_tire[$i]["Q3"] = trim($all_tire[$i]["Q3"] . "," . $all_tire[$i - 1]["Q3"], ",");
			} else {
				$all_tire[$i]["Q3"] = "";
			}

			if ($all_tire[$i]["Q4"] !== "" && $all_tire[$i]["Q4"] !== null) {
				$all_tire[$i]["Q4"] = trim($all_tire[$i]["Q4"] . "," . $all_tire[$i - 1]["Q4"], ",");
			} else {
				$all_tire[$i]["Q4"] = "";
			}
		}

		echo "<pre>" . print_r($all_tire, true) . "</pre>";
	}

	public function genbuildingPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/building'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");


		$item_group = $_POST['item_group'];

		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		$pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		//secho $dataBOIName ; exit();
		//$shiftBOI = $dataBOIName[0]["BOI"]
		//echo $dataBOIName; exit();

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));

		$arr = $this->report->BuildingServiceallpdf($datebuilding, $shift, $group, $product_group, $pressBOI);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					'Q5' => '',
					'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		if (isset($json_decode[0]->Description)) {
			$datagroup = $json_decode[0]->Description;
		} else {
			$datagroup = '';
		}

		//	print_r($json_decode); exit();
		// $datagroup = $json_decode[0]->Description;

		if ($check == 1) {
			renderView("pagemaster/pdf_building", [
				"dateinter" => $date_internal,
				"datajson" => $json_decode,
				"BOIName" => $dataBOIName
			]);
		}
		if ($check == 2) {
			renderView("report/building_excel", [
				"dateinter" => $date_internal,
				"datajson" => $json_decode,
				"BOIName" => $dataBOIName
			]);
		}
	}

	//nueng
	public function genshipdetailPDF()
	{

		$selectSingle = implode("','", $_POST["selectMenuData"]);
		$selectMulti = implode("'',''", $_POST["selectMenuData"]);

		$selectLoadid  = implode(',', $_POST["selectMenu"]);
		// $selectExternorderkey  = implode(',', $_POST["selectMenuData"]);

		$result = (new ReportService)->genshipdetailPDF($selectSingle,$selectMulti);

		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";
		// exit();
		// echo "<pre>";
		// print_r($result[0]);
		// echo "</pre>";
		// var_dump($result);
		// exit;

		if ($selectLoadid === "" || $selectExternorderkey === "") {
			echo 'data not found!';
		} else {
			if(count($_POST["selectMenuData"]) > 1)
			{
				renderView('page/shipdetails_topdf', [
					'data' => $result,
					'selectSingle' => $selectSingle,
					'selectMulti' => $selectMulti,
					'selectLoadid' => $selectLoadid
				]);
			}
			else
			{
				renderView('page/shipdetail_topdf', [
					'data' => $result,
					'selectSingle' => $selectSingle,
					'selectMulti' => $selectMulti,
					'selectLoadid' => $selectLoadid
				]);
			}
		}
		
	}

	public function genbuildingPDFt3()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/buildingt3'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");


		$item_group = $_POST['item_group'];

		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		$pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		//secho $dataBOIName ; exit();
		//$shiftBOI = $dataBOIName[0]["BOI"]
		//echo $dataBOIName; exit();

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));

		$arr = $this->report->Buildingt3Serviceallpdf($datebuilding, $shift, $group, $product_group, $pressBOI);
		$batch = $this->report->Buildingt3Batchpdf($datebuilding, $shift, $group, $product_group, $pressBOI);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					// 'Q5' => '',
					// 'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		if (isset($json_decode[0]->Description)) {
			$datagroup = $json_decode[0]->Description;
		} else {
			$datagroup = '';
		}

		//	echo "<pre>";print_r($json_decode); echo"</pre>";



		if ($check == 1) {
			renderView("pagemaster/pdf_buildingt3", [
				"dateinter" => $date_internal,
				"datajson" => $json_decode,
				"BOIName" => $dataBOIName,
				"Batch" => $batch
			]);
		}
		if ($check == 2) {
			renderView("report/building_excelt3", [
				"dateinter" => $date_internal,
				"datajson" => $json_decode,
				"BOIName" => $dataBOIName,
				"Batch" => $batch
			]);
		}
	}

	public function geninternalPDF()
	{
		$date_internal = filter_input(INPUT_POST, "date_internal");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$dateinter = date('Y-m-d', strtotime($date_internal));
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}



		$arr = $this->report->InternalServiceallpdf($dateinter, $pressBOI);
		
		//$arr = (new InternalService)->allpdf($dateinter);
		$json_decode  = json_decode($arr);
		
		$number = count(array_filter($json_decode));
		$numberall = (13 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
		];
		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				//$sorted = [];
				$json_decode[] = (object) [
					'Row' => '',
					'ItemID' => '',
					'time_create' => '',
					'TemplateSerialNo' => '',
					'NameTH' => '',
					'Note' => '',
					'Batch' => '',
					'qty' => '',
					'FirstName' => '',
					'Department' => '',
					'Name' => '',
				];
				$sorted = $json_decode;
			}
		}
		// echo "<pre>".print_r($json_decode,true)."</pre>";
		// exit();
		renderView("pagemaster/pdf_internal", [
			"dateinter" => $date_internal,
			"datajson" => $json_decode,
			"BOIName" => $dataBOIName
		]);
	}

	public function genActbuildingPDF()
	{
		//test 2
		$date_act = filter_input(INPUT_POST, "date");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_act));
		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		$rpt = new ReportService;
		$getmachine = $rpt->getMachineByDate($tstart, $tend);
		$report = $rpt->ActBuildServicepdf($tstart, $tend);

		// echo "<pre>".print_r($report,true)."</pre>";
		// exit;

		$sorted = [];

		foreach ($getmachine as $key => $value) {

			// echo $value["Machine"]." ".$value["Type"] ."**".$rpt->countUserAct($tstart, $tend ,  $value['Machine']);
			// echo "<br>";
			//-------CHK TYPE แล้วค่อยเชคจำนวน------

			$check_rows = $rpt->countUserAct($tstart, $tend, $value['Machine']);
			if ($value['Type'] === "TBR") {
				if ($check_rows === 0) {
					for ($i = 0; $i < 4; $i++) {
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 4,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'LoginDate' => "",
							'LogoutDate' => "",
							'SCH' => "",
							'Act' => "",
							'SCARP_MAC' => "",
							'TOTAL' => ""
						];
					}
				}
				if ($check_rows === 1) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => 4,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
					for ($i = 0; $i < 3; $i++) {
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 4,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'LoginDate' => "",
							'LogoutDate' => "",
							'SCH' => "",
							'Act' => "",
							'SCARP_MAC' => "",
							'TOTAL' => ""
						];
					}
				}
				if ($check_rows === 2) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => 4,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
					for ($i = 0; $i < 2; $i++) {
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 4,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'LoginDate' => "",
							'LogoutDate' => "",
							'SCH' => "",
							'Act' => "",
							'SCARP_MAC' => "",
							'TOTAL' => ""
						];
					}
				}
				if ($check_rows === 3) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => 4,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
					$sorted[] = [
						'Machine' => $value['Machine'],
						'rowspan' => 4,
						'EmployeeID' => "",
						'Name' => "",
						'BuildType' => "",
						'LoginDate' => "",
						'LogoutDate' => "",
						'SCH' => "",
						'Act' => "",
						'SCARP_MAC' => "",
						'TOTAL' => ""
					];
				}
				if ($check_rows >= 4) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => $check_rows,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
				}
			} else if ($value['Type'] === "PCR") {
				if ($check_rows === 0) {
					for ($i = 0; $i < 3; $i++) {
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 3,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'LoginDate' => "",
							'LogoutDate' => "",
							'SCH' => "",
							'Act' => "",
							'SCARP_MAC' => "",
							'TOTAL' => ""
						];
					}
				}
				if ($check_rows === 1) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => 3,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
					for ($i = 0; $i < 2; $i++) {
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 3,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'LoginDate' => "",
							'LogoutDate' => "",
							'SCH' => "",
							'Act' => "",
							'SCARP_MAC' => "",
							'TOTAL' => ""
						];
					}
				}
				if ($check_rows === 2) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => 3,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
					$sorted[] = [
						'Machine' => $value['Machine'],
						'rowspan' => 3,
						'EmployeeID' => "",
						'Name' => "",
						'BuildType' => "",
						'LoginDate' => "",
						'LogoutDate' => "",
						'SCH' => "",
						'Act' => "",
						'SCARP_MAC' => "",
						'TOTAL' => ""
					];
				}
				if ($check_rows >= 3) {
					foreach ($report as $r) {
						if ($r['Machine'] === $value['Machine']) {
							$sorted[] = [
								'Machine' => $r['Machine'],
								'rowspan' => $check_rows,
								'EmployeeID' => $r['EmployeeID'],
								'Name' => $r['Name'],
								'BuildType' => $r['BuildType'],
								'LoginDate' => $r['LoginDate'],
								'LogoutDate' => $r['LogoutDate'],
								'SCH' => $r['SCH'],
								'Act' => $r['Act'],
								'SCARP_MAC' => $r['SCARP_MAC'],
								'TOTAL' => $r['TOTAL']
							];
						}
					}
				}
			}
		}
		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		renderView("pagemaster/pdf_actbuild", [
			"data" => $sorted,
			"date" => $date,
			"shift" => $shift_th
		]);
	}

	public function genRatebuildingPDF()
	{

		$date_rate = filter_input(INPUT_POST, "date_rate");
		$line 		 = filter_input(INPUT_POST, "selectMenu");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_rate));

		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		$lineno  = convertforin(implode(',', $_POST["selectMenu"]));
		$rpt = new ReportService;

		$getmachine = $rpt->getMachineByLine($lineno);
		$chkmachine_ply = $rpt->chkMachinePLY($lineno);

		// $report = $rpt->RateBuildServicepdf($tstart,$tend,$lineno);
		if ($chkmachine_ply == true) {
			$report = $rpt->RateBuildServicepdf_Ply($tstart, $tend, $lineno);
			$check_allrows = $rpt->countUser_ALLLine_PLY($tstart, $tend, $lineno);

			// echo "<pre>".print_r($check_allrows,true)."</pre>";
			// echo "<pre>".print_r($lineno,true)."</pre>";
			// echo "<pre>".print_r($getmachine,true)."</pre>";
			// exit();

			if ($check_allrows === 0) {

				$sorted[] = [
					'rowspan' => 2,
					'colspan' => 13
				];
			} else {
				$sorted = [];
				foreach ($getmachine as $key => $value) {
					// echo $value["Machine"]."**".$rpt->countUser_PLY($tstart, $tend , $value['Machine']);
					// echo "<br>";
					$check_rows = $rpt->countUser_PLY($tstart, $tend, $value['Machine']);

					if ($check_rows === 0) {
						for ($i = 0; $i < 3; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Total' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 1) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 3,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => "Builder",
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Total' => $r['Total'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						for ($i = 0; $i < 2; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Total' => "",
								'Sum_Total' => ""
							];
						}
					}
					if ($check_rows === 2) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 3,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => "Builder",
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Total' => $r['Total'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 3,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'SCH' => "",
							'Act' => "",
							'P1' => "",
							'P2' => "",
							'P3' => "",
							'Total' => "",
							'Sum_Total' => "",
							'Qty1' => "",
							'Qty2' => "",
							'Qty3' => ""
						];
					}
					if ($check_rows >= 3) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 3,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => "Builder",
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Total' => $r['Total'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
					}
				}
			}
		} else {
			$report = $rpt->RateBuildServicepdf($tstart, $tend, $lineno);
			$check_allrows = $rpt->countUser_ALLLine($tstart, $tend, $lineno);
			if ($check_allrows === 0) {

				$sorted[] = [
					'rowspan' => 2,
					'colspan' => 13
				];
			} else {
				$sorted = [];
				foreach ($getmachine as $key => $value) {
					// echo $value["Machine"]." ".$value["Type"] ."**".$rpt->countUser($tstart, $tend , $value['Machine']); //."**".$sch->countItemExist($date, $shift, $value['Boiler'])
					// echo "<br>";
					$check_rows = $rpt->countUser($tstart, $tend, $value['Machine']);

					if ($value['Type'] === "TBR") {
						if ($check_rows === 0) {
							for ($i = 0; $i < 4; $i++) {
								$sorted[] = [
									'Machine' => $value['Machine'],
									'rowspan' => 4,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Sum_Total' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => ""
								];
							}
						}
						if ($check_rows === 1) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => 4,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
							for ($i = 0; $i < 3; $i++) {
								$sorted[] = [
									'Machine' => $value['Machine'],
									'rowspan' => 4,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Sum_Total' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => ""
								];
							}
						}
						if ($check_rows === 2) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => 4,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
							for ($i = 0; $i < 2; $i++) {
								$sorted[] = [
									'Machine' => $value['Machine'],
									'rowspan' => 4,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Sum_Total' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => ""
								];
							}
						}
						if ($check_rows === 3) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => 4,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
						if ($check_rows >= 4) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => $check_rows,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
						}
					} else if ($value['Type'] === "PCR") {
						if ($check_rows === 0) {
							for ($i = 0; $i < 3; $i++) {
								$sorted[] = [
									'Machine' => $value['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Sum_Total' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => ""
								];
							}
						}
						if ($check_rows === 1) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
							for ($i = 0; $i < 2; $i++) {
								$sorted[] = [
									'Machine' => $value['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Sum_Total' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => ""
								];
							}
						}
						if ($check_rows === 2) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
						if ($check_rows >= 3) {
							foreach ($report as $r) {
								if ($r['Machine'] === $value['Machine']) {
									$sorted[] = [
										'Machine' => $r['Machine'],
										'rowspan' => $check_rows,
										'EmployeeID' => $r['EmployeeID'],
										'Name' => $r['Name'],
										'BuildType' => $r['BuildType'],
										'SCH' => $r['SCH'],
										'Act' => $r['Act'],
										'P1' => $r['P1'],
										'P2' => $r['P2'],
										'P3' => $r['P3'],
										'Charge' => $r['Charge'],
										'Total' => $r['Total'],
										'Total_Diff' => $r['Total_Diff'],
										'Sum_Total' => $r['Sum_Total'],
										'Qty1' => $r['Qty1'],
										'Qty2' => $r['Qty2'],
										'Qty3' => $r['Qty3']
									];
								}
							}
						}
					}
				}
			}
		}
		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		renderView("pagemaster/pdf_ratebuild", [
			"data" => $sorted,
			"date" => $date_rate,
			"shift" => $shift_th
		]);
	}

	public function gencuringPDF()
	{
		// exit('This section in maintenance for a moment, sorry for your inconvenience.');
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press 		 = filter_input(INPUT_POST, "selectMenu");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$datecuring = date('Y-m-d', strtotime($date_curing));

		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}
		$pressno  = convertforin(implode(',', $_POST["selectMenu"]));

		$A = array("A", "C", "E", "G", "K");
		$B = array("B", "D", "F", "H");
		$I = array("I");
		$J = array("J");
		$L = array("L");
		$M = array("M");
		$N = array("N");
		$O = array("O");
		$P = array("P");
		$Dummy = array("dummy");

		if (in_array($pressno, $Dummy)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_dummy";
			$fake = 12;
		}
		if (in_array($pressno, $A)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_a";
			$fake = 12;
		}
		if (in_array($pressno, $B)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing";
			$fake = 12;

			// if ($pressno === "L") {
			// 	$pagecuring = "pdf_curing_i";
			// 	$fake = 13;
			// }
		}
		if (in_array($pressno, $I)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_i";
			$fake = 13;
		}
		if (in_array($pressno, $J)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_i";
			$fake = 13;
		}

		if (in_array($pressno, $L)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_l";
			$fake = 21;
		}

		if (in_array($pressno, $M)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_m";
			$fake = 16;
		}

		if (in_array($pressno, $N)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_n";
			$fake = 16;
		}

		if (in_array($pressno, $O)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_o";
			$fake = 20;
		}

		if (in_array($pressno, $P)) {
			$press1 = $pressno;
			$pagecuring = "pdf_curing_p";
			$fake = 20;
		}

		// return $press1;exit;

		
		function quick_sort($array)
		{
			$length = count($array);

			if ($length <= 1) {
				return $array;
			} else {

				$pivot = $array[0];

				$left = $right = array();

				for ($i = 1; $i < count($array); $i++) {
					if ($array[$i] < $pivot) {
						$left[] = $array[$i];
					} else {
						$right[] = $array[$i];
					}
				}
				return array_merge(quick_sort($left), array($pivot), quick_sort($right));
			}
		}
		

		if (isset($press1)) {

			// if($press1 == 'dummy'){
			// 	$arr1 = $this->report->CuringServiceallpdfDummy($datecuring, $shift, $press1);
			// }else{
			$arr1 = $this->report->CuringServiceallpdf1($datecuring, $shift, $press1);
			// }
			$getDataQ1 = $this->report->CuringServiceallpdfQ1($datecuring, $shift, $press1);
			$getDataDummy = $this->report->CuringServiceallpdfQ1Dummy($datecuring, $shift, $press1);
			
			// var_dump($arr1);exit();
			// echo "<pre>";
			// // print_r(json_decode($arr1));
			// print_r($getDataQ1);
			// echo "</pre>";
			// exit();
			$press01 = $press1 . "01";
			$press04 = $press1 . "04";
			$press05 = $press1 . "05";
			$press08 = $press1 . "08";
			$press09 = $press1 . "09";
			$press12 = $press1 . "12";
			$press13 = $press1 . "13";
		}
		if ($fake == 12) {
			$fake_data = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0] //12
			];
		} else if ($fake == 20) {
			$fake_data = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0], //12
				[0, 0], //13
				[0, 0], //14
				[0, 0], //15
				[0, 0], //16
				[0, 0], //17
				[0, 0],	//18
				[0, 0], //19
				[0, 0] //20

			];
		} else if ($fake == 21) {
			$fake_data = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0], //12
				[0, 0], //13
				[0, 0], //14
				[0, 0], //15
				[0, 0], //16
				[0, 0], //17
				[0, 0],	//18
				[0, 0], //19
				[0, 0], //20
				[0, 0] //21

			];
		} else if ($fake == 16) {
			$fake_data = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0], //12
				[0, 0], //13
				[0, 0], //14
				[0, 0], //15
				[0, 0], //16


			];
		} else {
			$fake_data = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0], //12
				[0, 0]  //13
			];
		}
		
		if (isset($press1)) {
			if (!isset($arr1)) {
				for ($i = 0; $i < $fake; $i++) {
					foreach ($fake_data[$i] as $value) {
						$sorted = [];
						$json_decode1[] = (object) [
							'PressNo' => $press1 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => '',
							'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
						$sorted[] = $json_decode1;
					}
				}
			} else {

				$dataDecode  = json_decode($arr1);
				// $dataDecode  = json_decode($getDataDummy);

				foreach ($dataDecode as $data) {
					$json_decode1[] = (object) [
						'PressNo' => $data->PressNo,
						'PressSide' => $data->PressSide,
						'CuringCode' => '',
						'Q1' => $data->Q1,
						'Q2' => $data->Q2,
						'Q3' => $data->Q3,
						'Q4' => $data->Q4,
					];
				}
			}

			//echo "<pre>".print_r($json_decode1,true)."</pre>";
			$me = [];

			foreach ($json_decode1 as $value) {
				$me[] = [(int) substr($value->PressNo, 1), $value->PressSide];
			}



			foreach ($me as $value) {
				if ($value[1] === 'L') {
					$fake_data[$value[0] - 1][0] = 1;
				} else if ($value[1] === 'R') {
					$fake_data[$value[0] - 1][1] = 1;
				}
			}

			for ($i = 0; $i < $fake; $i++) {
				foreach ($fake_data[$i] as $value) {

					if ($value === 0) {
						$json_decode1[] = (object) [
							'PressNo' => $press1 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => 'R',
							'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
					}
				}
			}

			$sorted = quick_sort($json_decode1);
		}
		// echo "<pre>";
		// print_r(json_decode($getDataDummy));
		// echo "</pre>";
		// exit();
		// echo "<pre>".print_r($getDataQ1,true)."</pre>";
		// echo "<pre>" . print_r($sorted, true) . "</pre>";
		// exit();
		//name1

		// $dataname1 = $this->report->Curingname1_4($datecuring,$shift,$press01,$press04);
		//      	$dataname2 = $this->report->Curingname5_8($datecuring,$shift,$press05,$press08);
		//      	$dataname3 = $this->report->Curingname9_12($datecuring,$shift,$press09,$press12);
		$sorted2  = [];
		// error_reporting(E_ALL ^ E_NOTICE);

		// echo "<pre>" . print_r($sorted, true) . "</pre>";
		// exit;

		if (!isset($sorted[24]->PressNo)) {
			$b13 = "";
		} else {
			$b13 = 	$sorted[24]->PressNo;
		}

		if (!isset($sorted[26]->PressNo)) {
			$b14 = "";
		} else {
			$b14 = 	$sorted[26]->PressNo;
		}

		if (!isset($sorted[28]->PressNo)) {
			$b15 = "";
		} else {
			$b15 = 	$sorted[28]->PressNo;
		}

		if (!isset($sorted[30]->PressNo)) {
			$b16 = "";
		} else {
			$b16 = $sorted[30]->PressNo;
		}

		if (!isset($sorted[32]->PressNo)) {
			$b17 = "";
		} else {
			$b17 = 	$sorted[32]->PressNo;
		}

		if (!isset($sorted[34]->PressNo)) {
			$b18 = "";
		} else {
			$b18 = 	$sorted[34]->PressNo;
		}

		if (!isset($sorted[36]->PressNo)) {
			$b19 = "";
		} else {
			$b19 = 	$sorted[36]->PressNo;
		}

		if (!isset($sorted[38]->PressNo)) {
			$b20 = "";
		} else {
			$b20 = 	$sorted[38]->PressNo;
		}

		if (!isset($sorted[40]->PressNo)) {
			$b21 = "";
		} else {
			$b21 = 	$sorted[40]->PressNo;
		}

		// echo "<pre>" . print_r($b20, true) . "</pre>";
		// echo "<pre>" . print_r($b21, true) . "</pre>";
		// exit();
		// var_dump($sorted[28]->PressNo);
		// exit;
		$group = $this->report->CuringServiceallgrouppdf($datecuring, $shift);
		$group_decode  = json_decode($group);
		renderView("pagemaster/" . $pagecuring, [
			"datajsonQ" => $getDataQ1,
			"dataDummy" => $getDataDummy,
			"group_decode" => $group_decode,
			"pressNo" => $pressno,
			"press1" => $press1,
			// "press2" => $press2,
			// "press3" => $press3,
			"datecuring" => $date_curing,
			"shift" => $shift,
			"datajson" => $sorted,
			"b01" => $sorted[0]->PressNo,
			"b02" => $sorted[2]->PressNo,
			"b03" => $sorted[4]->PressNo,
			"b04" => $sorted[6]->PressNo,
			"b05" => $sorted[8]->PressNo,
			"b06" => $sorted[10]->PressNo,
			"b07" => $sorted[12]->PressNo,
			"b08" => $sorted[14]->PressNo,
			"b09" => $sorted[16]->PressNo,
			"b10" => $sorted[18]->PressNo,
			"b11" => $sorted[20]->PressNo,
			"b12" => $sorted[22]->PressNo,
			"b13" => $b13,
			"b14" => $b14,
			"b15" => $b15,
			"b16" => $b16,
			"b17" => $b17,
			"b18" => $b18,
			"b19" => $b19,
			"b20" => $b20,
			"b21" => $b21,
			"cur1" => $sorted[0]->CuringCode,
			"cur2" => $sorted[1]->CuringCode,
			"cur3" => $sorted[2]->CuringCode,
			"cur4" => $sorted[3]->CuringCode,
			"cur5" => $sorted[4]->CuringCode,
			"cur6" => $sorted[5]->CuringCode,
			"cur7" => $sorted[6]->CuringCode,
			"cur8" => $sorted[7]->CuringCode,
			"cur9" => $sorted[8]->CuringCode,
			"cur10" => $sorted[9]->CuringCode,
			"cur11" => $sorted[10]->CuringCode,
			"cur12" => $sorted[11]->CuringCode,
			"cur13" => $sorted[12]->CuringCode,
			"cur14" => $sorted[13]->CuringCode,
			"cur15" => $sorted[14]->CuringCode,
			"cur16" => $sorted[15]->CuringCode,
			"cur17" => $sorted[16]->CuringCode,
			"cur18" => $sorted[17]->CuringCode,
			"cur19" => $sorted[18]->CuringCode,
			"cur20" => $sorted[19]->CuringCode,
			"cur21" => $sorted[20]->CuringCode,
			"cur22" => $sorted[21]->CuringCode,
			"cur23" => $sorted[22]->CuringCode,
			"cur24" => $sorted[23]->CuringCode,
			"cur25" => $sorted[24]->CuringCode,
			"cur26" => $sorted[25]->CuringCode,
			"qty11" => $sorted[0]->Q1,
			"qty21" => $sorted[0]->Q2,
			"qty31" => $sorted[0]->Q3,
			"qty41" => $sorted[0]->Q4,
			"qty12" => $sorted[1]->Q1,
			"qty22" => $sorted[1]->Q2,
			"qty32" => $sorted[1]->Q3,
			"qty42" => $sorted[1]->Q4,
			"qty13" => $sorted[2]->Q1,
			"qty23" => $sorted[2]->Q2,
			"qty33" => $sorted[2]->Q3,
			"qty43" => $sorted[2]->Q4,
			"qty14" => $sorted[3]->Q1,
			"qty24" => $sorted[3]->Q2,
			"qty34" => $sorted[3]->Q3,
			"qty44" => $sorted[3]->Q4,
			"qty15" => $sorted[4]->Q1,
			"qty25" => $sorted[4]->Q2,
			"qty35" => $sorted[4]->Q3,
			"qty45" => $sorted[4]->Q4,
			"qty16" => $sorted[5]->Q1,
			"qty26" => $sorted[5]->Q2,
			"qty36" => $sorted[5]->Q3,
			"qty46" => $sorted[5]->Q4,
			"qty17" => $sorted[6]->Q1,
			"qty27" => $sorted[6]->Q2,
			"qty37" => $sorted[6]->Q3,
			"qty47" => $sorted[6]->Q4,
			"qty18" => $sorted[7]->Q1,
			"qty28" => $sorted[7]->Q2,
			"qty38" => $sorted[7]->Q3,
			"qty48" => $sorted[7]->Q4,
			"qty19" => $sorted[8]->Q1,
			"qty29" => $sorted[8]->Q2,
			"qty39" => $sorted[8]->Q3,
			"qty49" => $sorted[8]->Q4,
			"qty110" => $sorted[9]->Q1,
			"qty210" => $sorted[9]->Q2,
			"qty310" => $sorted[9]->Q3,
			"qty410" => $sorted[9]->Q4,
			"qty111" => $sorted[10]->Q1,
			"qty211" => $sorted[10]->Q2,
			"qty311" => $sorted[10]->Q3,
			"qty411" => $sorted[10]->Q4,
			"qty112" => $sorted[11]->Q1,
			"qty212" => $sorted[11]->Q2,
			"qty312" => $sorted[11]->Q3,
			"qty412" => $sorted[11]->Q4,
			"qty113" => $sorted[12]->Q1,
			"qty213" => $sorted[12]->Q2,
			"qty313" => $sorted[12]->Q3,
			"qty413" => $sorted[12]->Q4,
			"qty114" => $sorted[13]->Q1,
			"qty214" => $sorted[13]->Q2,
			"qty314" => $sorted[13]->Q3,
			"qty414" => $sorted[13]->Q4,
			"qty115" => $sorted[14]->Q1,
			"qty215" => $sorted[14]->Q2,
			"qty315" => $sorted[14]->Q3,
			"qty415" => $sorted[14]->Q4,
			"qty116" => $sorted[15]->Q1,
			"qty216" => $sorted[15]->Q2,
			"qty316" => $sorted[15]->Q3,
			"qty416" => $sorted[15]->Q4,
			"qty117" => $sorted[16]->Q1,
			"qty217" => $sorted[16]->Q2,
			"qty317" => $sorted[16]->Q3,
			"qty417" => $sorted[16]->Q4,
			"qty118" => $sorted[17]->Q1,
			"qty218" => $sorted[17]->Q2,
			"qty318" => $sorted[17]->Q3,
			"qty418" => $sorted[17]->Q4,
			"qty119" => $sorted[18]->Q1,
			"qty219" => $sorted[18]->Q2,
			"qty319" => $sorted[18]->Q3,
			"qty419" => $sorted[18]->Q4,
			"qty1110" => $sorted[19]->Q1,
			"qty2110" => $sorted[19]->Q2,
			"qty3110" => $sorted[19]->Q3,
			"qty4110" => $sorted[19]->Q4,
			"qty1111" => $sorted[20]->Q1,
			"qty2111" => $sorted[20]->Q2,
			"qty3111" => $sorted[20]->Q3,
			"qty4111" => $sorted[20]->Q4,
			"qty1112" => $sorted[21]->Q1,
			"qty2112" => $sorted[21]->Q2,
			"qty3112" => $sorted[21]->Q3,
			"qty4112" => $sorted[21]->Q4,
			"qty1113" => $sorted[22]->Q1,
			"qty2113" => $sorted[22]->Q2,
			"qty3113" => $sorted[22]->Q3,
			"qty4113" => $sorted[22]->Q4,
			"qty1114" => $sorted[23]->Q1,
			"qty2114" => $sorted[23]->Q2,
			"qty3114" => $sorted[23]->Q3,
			"qty4114" => $sorted[23]->Q4,
			"qty1115" => $sorted[24]->Q1,
			"qty2115" => $sorted[24]->Q2,
			"qty3115" => $sorted[24]->Q3,
			"qty4115" => $sorted[24]->Q4,
			"qty1116" => $sorted[25]->Q1,
			"qty2116" => $sorted[25]->Q2,
			"qty3116" => $sorted[25]->Q3,
			"qty4116" => $sorted[25]->Q4,

			"qty1117" => $sorted[26]->Q1,
			"qty2117" => $sorted[26]->Q2,
			"qty3117" => $sorted[26]->Q3,
			"qty4117" => $sorted[26]->Q4,
			"qty1118" => $sorted[27]->Q1,
			"qty2118" => $sorted[27]->Q2,
			"qty3118" => $sorted[27]->Q3,
			"qty4118" => $sorted[27]->Q4,
			"qty1119" => $sorted[28]->Q1,
			"qty2119" => $sorted[28]->Q2,
			"qty3119" => $sorted[28]->Q3,
			"qty4119" => $sorted[28]->Q4,
			"qty1120" => $sorted[29]->Q1,
			"qty2120" => $sorted[29]->Q2,
			"qty3120" => $sorted[29]->Q3,
			"qty4120" => $sorted[29]->Q4,
			"qty1121" => $sorted[30]->Q1,
			"qty2121" => $sorted[30]->Q2,
			"qty3121" => $sorted[30]->Q3,
			"qty4121" => $sorted[30]->Q4,
			"qty1122" => $sorted[31]->Q1,
			"qty2122" => $sorted[31]->Q2,
			"qty3122" => $sorted[31]->Q3,
			"qty4122" => $sorted[31]->Q4,
			"qty1123" => $sorted[32]->Q1,
			"qty2123" => $sorted[32]->Q2,
			"qty3123" => $sorted[32]->Q3,
			"qty4123" => $sorted[32]->Q4,
			"qty1124" => $sorted[33]->Q1,
			"qty2124" => $sorted[33]->Q2,
			"qty3124" => $sorted[33]->Q3,
			"qty4124" => $sorted[33]->Q4,
			"qty1125" => $sorted[34]->Q1,
			"qty2125" => $sorted[34]->Q2,
			"qty3125" => $sorted[34]->Q3,
			"qty4125" => $sorted[34]->Q4,
			"qty1126" => $sorted[35]->Q1,
			"qty2126" => $sorted[35]->Q2,
			"qty3126" => $sorted[35]->Q3,
			"qty4126" => $sorted[35]->Q4,
			"qty1127" => $sorted[36]->Q1,
			"qty2127" => $sorted[36]->Q2,
			"qty3127" => $sorted[36]->Q3,
			"qty4127" => $sorted[36]->Q4,
			"qty1128" => $sorted[37]->Q1,
			"qty2128" => $sorted[37]->Q2,
			"qty3128" => $sorted[37]->Q3,
			"qty4128" => $sorted[37]->Q4,
			"qty1129" => $sorted[38]->Q1,
			"qty2129" => $sorted[38]->Q2,
			"qty3129" => $sorted[38]->Q3,
			"qty4129" => $sorted[38]->Q4,
			"qty1130" => $sorted[39]->Q1,
			"qty2130" => $sorted[39]->Q2,
			"qty3130" => $sorted[39]->Q3,
			"qty4130" => $sorted[39]->Q4,
			"qty1131" => $sorted[40]->Q1,
			"qty2131" => $sorted[40]->Q2,
			"qty3131" => $sorted[40]->Q3,
			"qty4131" => $sorted[40]->Q4,
			"qty1132" => $sorted[41]->Q1,
			"qty2132" => $sorted[41]->Q2,
			"qty3132" => $sorted[41]->Q3,
			"qty4132" => $sorted[41]->Q4,

			// "dataname_sec1" => $dataname_sec1,
			// "dataname_sec2" => $dataname_sec2,
			// "dataname_sec3" => $dataname_sec3,
			"datajsonQ2" => $getDataQ2,
			"datajson2" => $sorted2,
			"b01_sec" => $sorted2[0]->PressNo,
			"b02_sec" => $sorted2[2]->PressNo,
			"b03_sec" => $sorted2[4]->PressNo,
			"b04_sec" => $sorted2[6]->PressNo,
			"b05_sec" => $sorted2[8]->PressNo,
			"b06_sec" => $sorted2[10]->PressNo,
			"b07_sec" => $sorted2[12]->PressNo,
			"b08_sec" => $sorted2[14]->PressNo,
			"b09_sec" => $sorted2[16]->PressNo,
			"b10_sec" => $sorted2[18]->PressNo,
			"b11_sec" => $sorted2[20]->PressNo,
			"b12_sec" => $sorted2[22]->PressNo,
			"cur1_sec" => $sorted2[0]->CuringCode,
			"cur2_sec" => $sorted2[1]->CuringCode,
			"cur3_sec" => $sorted2[2]->CuringCode,
			"cur4_sec" => $sorted2[3]->CuringCode,
			"cur5_sec" => $sorted2[4]->CuringCode,
			"cur6_sec" => $sorted2[5]->CuringCode,
			"cur7_sec" => $sorted2[6]->CuringCode,
			"cur8_sec" => $sorted2[7]->CuringCode,
			"cur9_sec" => $sorted2[8]->CuringCode,
			"cur10_sec" => $sorted2[9]->CuringCode,
			"cur11_sec" => $sorted2[10]->CuringCode,
			"cur12_sec" => $sorted2[11]->CuringCode,
			"cur13_sec" => $sorted2[12]->CuringCode,
			"cur14_sec" => $sorted2[13]->CuringCode,
			"cur15_sec" => $sorted2[14]->CuringCode,
			"cur16_sec" => $sorted2[15]->CuringCode,
			"cur17_sec" => $sorted2[16]->CuringCode,
			"cur18_sec" => $sorted2[17]->CuringCode,
			"cur19_sec" => $sorted2[18]->CuringCode,
			"cur20_sec" => $sorted2[19]->CuringCode,
			"cur21_sec" => $sorted2[20]->CuringCode,
			"cur22_sec" => $sorted2[21]->CuringCode,
			"cur23_sec" => $sorted2[22]->CuringCode,
			"cur24_sec" => $sorted2[23]->CuringCode,
			"qty11_sec" => $sorted2[0]->Q1,
			"qty21_sec" => $sorted2[0]->Q2,
			"qty31_sec" => $sorted2[0]->Q3,
			"qty41_sec" => $sorted2[0]->Q4,
			"qty12_sec" => $sorted2[1]->Q1,
			"qty22_sec" => $sorted2[1]->Q2,
			"qty32_sec" => $sorted2[1]->Q3,
			"qty42_sec" => $sorted2[1]->Q4,
			"qty13_sec" => $sorted2[2]->Q1,
			"qty23_sec" => $sorted2[2]->Q2,
			"qty33_sec" => $sorted2[2]->Q3,
			"qty43_sec" => $sorted2[2]->Q4,
			"qty14_sec" => $sorted2[3]->Q1,
			"qty24_sec" => $sorted2[3]->Q2,
			"qty34_sec" => $sorted2[3]->Q3,
			"qty44_sec" => $sorted2[3]->Q4,
			"qty15_sec" => $sorted2[4]->Q1,
			"qty25_sec" => $sorted2[4]->Q2,
			"qty35_sec" => $sorted2[4]->Q3,
			"qty45_sec" => $sorted2[4]->Q4,
			"qty16_sec" => $sorted2[5]->Q1,
			"qty26_sec" => $sorted2[5]->Q2,
			"qty36_sec" => $sorted2[5]->Q3,
			"qty46_sec" => $sorted2[5]->Q4,
			"qty17_sec" => $sorted2[6]->Q1,
			"qty27_sec" => $sorted2[6]->Q2,
			"qty37_sec" => $sorted2[6]->Q3,
			"qty47_sec" => $sorted2[6]->Q4,
			"qty18_sec" => $sorted2[7]->Q1,
			"qty28_sec" => $sorted2[7]->Q2,
			"qty38_sec" => $sorted2[7]->Q3,
			"qty48_sec" => $sorted2[7]->Q4,
			"qty19_sec" => $sorted2[8]->Q1,
			"qty29_sec" => $sorted2[8]->Q2,
			"qty39_sec" => $sorted2[8]->Q3,
			"qty49_sec" => $sorted2[8]->Q4,
			"qty110_sec" => $sorted2[9]->Q1,
			"qty210_sec" => $sorted2[9]->Q2,
			"qty310_sec" => $sorted2[9]->Q3,
			"qty410_sec" => $sorted2[9]->Q4,
			"qty111_sec" => $sorted2[10]->Q1,
			"qty211_sec" => $sorted2[10]->Q2,
			"qty311_sec" => $sorted2[10]->Q3,
			"qty411_sec" => $sorted2[10]->Q4,
			"qty112_sec" => $sorted2[11]->Q1,
			"qty212_sec" => $sorted2[11]->Q2,
			"qty312_sec" => $sorted2[11]->Q3,
			"qty412_sec" => $sorted2[11]->Q4,
			"qty113_sec" => $sorted2[12]->Q1,
			"qty213_sec" => $sorted2[12]->Q2,
			"qty313_sec" => $sorted2[12]->Q3,
			"qty413_sec" => $sorted2[12]->Q4,
			"qty114_sec" => $sorted2[13]->Q1,
			"qty214_sec" => $sorted2[13]->Q2,
			"qty314_sec" => $sorted2[13]->Q3,
			"qty414_sec" => $sorted2[13]->Q4,
			"qty115_sec" => $sorted2[14]->Q1,
			"qty215_sec" => $sorted2[14]->Q2,
			"qty315_sec" => $sorted2[14]->Q3,
			"qty415_sec" => $sorted2[14]->Q4,
			"qty116_sec" => $sorted2[15]->Q1,
			"qty216_sec" => $sorted2[15]->Q2,
			"qty316_sec" => $sorted2[15]->Q3,
			"qty416_sec" => $sorted2[15]->Q4,
			"qty117_sec" => $sorted2[16]->Q1,
			"qty217_sec" => $sorted2[16]->Q2,
			"qty317_sec" => $sorted2[16]->Q3,
			"qty417_sec" => $sorted2[16]->Q4,
			"qty118_sec" => $sorted2[17]->Q1,
			"qty218_sec" => $sorted2[17]->Q2,
			"qty318_sec" => $sorted2[17]->Q3,
			"qty418_sec" => $sorted2[17]->Q4,
			"qty119_sec" => $sorted2[18]->Q1,
			"qty219_sec" => $sorted2[18]->Q2,
			"qty319_sec" => $sorted2[18]->Q3,
			"qty419_sec" => $sorted2[18]->Q4,
			"qty1110_sec" => $sorted2[19]->Q1,
			"qty2110_sec" => $sorted2[19]->Q2,
			"qty3110_sec" => $sorted2[19]->Q3,
			"qty4110_sec" => $sorted2[19]->Q4,
			"qty1111_sec" => $sorted2[20]->Q1,
			"qty2111_sec" => $sorted2[20]->Q2,
			"qty3111_sec" => $sorted2[20]->Q3,
			"qty4111_sec" => $sorted2[20]->Q4,
			"qty1112_sec" => $sorted2[21]->Q1,
			"qty2112_sec" => $sorted2[21]->Q2,
			"qty3112_sec" => $sorted2[21]->Q3,
			"qty4112_sec" => $sorted2[21]->Q4,
			"qty1113_sec" => $sorted2[22]->Q1,
			"qty2113_sec" => $sorted2[22]->Q2,
			"qty3113_sec" => $sorted2[22]->Q3,
			"qty4113_sec" => $sorted2[22]->Q4,
			"qty1114_sec" => $sorted2[23]->Q1,
			"qty2114_sec" => $sorted2[23]->Q2,
			"qty3114_sec" => $sorted2[23]->Q3,
			"qty4114_sec" => $sorted2[23]->Q4,
			// "dataname_third1" => $dataname_third1,
			// "dataname_third2" => $dataname_third2,
			// "dataname_third3" => $dataname_third3,
			"datajsonQ3" => $getDataQ3,
			"datajson3" => $sorted3,
			"b01_third" => $sorted3[0]->PressNo,
			"b02_third" => $sorted3[2]->PressNo,
			"b03_third" => $sorted3[4]->PressNo,
			"b04_third" => $sorted3[6]->PressNo,
			"b05_third" => $sorted3[8]->PressNo,
			"b06_third" => $sorted3[10]->PressNo,
			"b07_third" => $sorted3[12]->PressNo,
			"b08_third" => $sorted3[14]->PressNo,
			"b09_third" => $sorted3[16]->PressNo,
			"b10_third" => $sorted3[18]->PressNo,
			"b11_third" => $sorted3[20]->PressNo,
			"b12_third" => $sorted3[22]->PressNo,
			"cur1_third" => $sorted3[0]->CuringCode,
			"cur2_third" => $sorted3[1]->CuringCode,
			"cur3_third" => $sorted3[2]->CuringCode,
			"cur4_third" => $sorted3[3]->CuringCode,
			"cur5_third" => $sorted3[4]->CuringCode,
			"cur6_third" => $sorted3[5]->CuringCode,
			"cur7_third" => $sorted3[6]->CuringCode,
			"cur8_third" => $sorted3[7]->CuringCode,
			"cur9_third" => $sorted3[8]->CuringCode,
			"cur10_third" => $sorted3[9]->CuringCode,
			"cur11_third" => $sorted3[10]->CuringCode,
			"cur12_third" => $sorted3[11]->CuringCode,
			"cur13_third" => $sorted3[12]->CuringCode,
			"cur14_third" => $sorted3[13]->CuringCode,
			"cur15_third" => $sorted3[14]->CuringCode,
			"cur16_third" => $sorted3[15]->CuringCode,
			"cur17_third" => $sorted3[16]->CuringCode,
			"cur18_third" => $sorted3[17]->CuringCode,
			"cur19_third" => $sorted3[18]->CuringCode,
			"cur20_third" => $sorted3[19]->CuringCode,
			"cur21_third" => $sorted3[20]->CuringCode,
			"cur22_third" => $sorted3[21]->CuringCode,
			"cur23_third" => $sorted3[22]->CuringCode,
			"cur24_third" => $sorted3[23]->CuringCode,
			"qty11_third" => $sorted3[0]->Q1,
			"qty21_third" => $sorted3[0]->Q2,
			"qty31_third" => $sorted3[0]->Q3,
			"qty41_third" => $sorted3[0]->Q4,
			"qty12_third" => $sorted3[1]->Q1,
			"qty22_third" => $sorted3[1]->Q2,
			"qty32_third" => $sorted3[1]->Q3,
			"qty42_third" => $sorted3[1]->Q4,
			"qty13_third" => $sorted3[2]->Q1,
			"qty23_third" => $sorted3[2]->Q2,
			"qty33_third" => $sorted3[2]->Q3,
			"qty43_third" => $sorted3[2]->Q4,
			"qty14_third" => $sorted3[3]->Q1,
			"qty24_third" => $sorted3[3]->Q2,
			"qty34_third" => $sorted3[3]->Q3,
			"qty44_third" => $sorted3[3]->Q4,
			"qty15_third" => $sorted3[4]->Q1,
			"qty25_third" => $sorted3[4]->Q2,
			"qty35_third" => $sorted3[4]->Q3,
			"qty45_third" => $sorted3[4]->Q4,
			"qty16_third" => $sorted3[5]->Q1,
			"qty26_third" => $sorted3[5]->Q2,
			"qty36_third" => $sorted3[5]->Q3,
			"qty46_third" => $sorted3[5]->Q4,
			"qty17_third" => $sorted3[6]->Q1,
			"qty27_third" => $sorted3[6]->Q2,
			"qty37_third" => $sorted3[6]->Q3,
			"qty47_third" => $sorted3[6]->Q4,
			"qty18_third" => $sorted3[7]->Q1,
			"qty28_third" => $sorted3[7]->Q2,
			"qty38_third" => $sorted3[7]->Q3,
			"qty48_third" => $sorted3[7]->Q4,
			"qty19_third" => $sorted3[8]->Q1,
			"qty29_third" => $sorted3[8]->Q2,
			"qty39_third" => $sorted3[8]->Q3,
			"qty49_third" => $sorted3[8]->Q4,
			"qty110_third" => $sorted3[9]->Q1,
			"qty210_third" => $sorted3[9]->Q2,
			"qty310_third" => $sorted3[9]->Q3,
			"qty410_third" => $sorted3[9]->Q4,
			"qty111_third" => $sorted3[10]->Q1,
			"qty211_third" => $sorted3[10]->Q2,
			"qty311_third" => $sorted3[10]->Q3,
			"qty411_third" => $sorted3[10]->Q4,
			"qty112_third" => $sorted3[11]->Q1,
			"qty212_third" => $sorted3[11]->Q2,
			"qty312_third" => $sorted3[11]->Q3,
			"qty412_third" => $sorted3[11]->Q4,
			"qty113_third" => $sorted3[12]->Q1,
			"qty213_third" => $sorted3[12]->Q2,
			"qty313_third" => $sorted3[12]->Q3,
			"qty413_third" => $sorted3[12]->Q4,
			"qty114_third" => $sorted3[13]->Q1,
			"qty214_third" => $sorted3[13]->Q2,
			"qty314_third" => $sorted3[13]->Q3,
			"qty414_third" => $sorted3[13]->Q4,
			"qty115_third" => $sorted3[14]->Q1,
			"qty215_third" => $sorted3[14]->Q2,
			"qty315_third" => $sorted3[14]->Q3,
			"qty415_third" => $sorted3[14]->Q4,
			"qty116_third" => $sorted3[15]->Q1,
			"qty216_third" => $sorted3[15]->Q2,
			"qty316_third" => $sorted3[15]->Q3,
			"qty416_third" => $sorted3[15]->Q4,
			"qty117_third" => $sorted3[16]->Q1,
			"qty217_third" => $sorted3[16]->Q2,
			"qty317_third" => $sorted3[16]->Q3,
			"qty417_third" => $sorted3[16]->Q4,
			"qty118_third" => $sorted3[17]->Q1,
			"qty218_third" => $sorted3[17]->Q2,
			"qty318_third" => $sorted3[17]->Q3,
			"qty418_third" => $sorted3[17]->Q4,
			"qty119_third" => $sorted3[18]->Q1,
			"qty219_third" => $sorted3[18]->Q2,
			"qty319_third" => $sorted3[18]->Q3,
			"qty419_third" => $sorted3[18]->Q4,
			"qty1110_third" => $sorted3[19]->Q1,
			"qty2110_third" => $sorted3[19]->Q2,
			"qty3110_third" => $sorted3[19]->Q3,
			"qty4110_third" => $sorted3[19]->Q4,
			"qty1111_third" => $sorted3[20]->Q1,
			"qty2111_third" => $sorted3[20]->Q2,
			"qty3111_third" => $sorted3[20]->Q3,
			"qty4111_third" => $sorted3[20]->Q4,
			"qty1112_third" => $sorted3[21]->Q1,
			"qty2112_third" => $sorted3[21]->Q2,
			"qty3112_third" => $sorted3[21]->Q3,
			"qty4112_third" => $sorted3[21]->Q4,
			"qty1113_third" => $sorted3[22]->Q1,
			"qty2113_third" => $sorted3[22]->Q2,
			"qty3113_third" => $sorted3[22]->Q3,
			"qty4113_third" => $sorted3[22]->Q4,
			"qty1114_third" => $sorted3[23]->Q1,
			"qty2114_third" => $sorted3[23]->Q2,
			"qty3114_third" => $sorted3[23]->Q3,
			"qty4114_third" => $sorted3[23]->Q4

		]);
	}

	public function geninventoryPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/greentire/inventory'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';


		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		// $arr = $this->report->GreentireInventoryServiceallpdf();
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		$arr = $this->report->greentireInventoryV2($product_group, $pressBOI);

		$json_decode  = json_decode($arr);

		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		renderView("pagemaster/pdf_inventory", [
			"datajson" => $json_decode,
			"date" => $date,
			"time" => $time,
			"BOIName" => $dataBOIName
		]);
	}

	public function reportPCRToSVO()
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$warehouse = filter_input(INPUT_POST, "warehouse");
		$location_type = $_POST['location_type'];
		$datewarehouse = date('Y-m-d', strtotime($date));
		$datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$time_selected = $_POST["selecttime"];
		$timeset = [];

		foreach ($time_selected as $k => $time_id) {
			if ($time_id === '1') {
				array_push($timeset, '\'' . $datewarehouse . ' 08:00:00\' AND ' . '\'' . $datewarehouse . ' 09:59:59\'');
			} else if ($time_id === '2') {
				array_push($timeset, '\'' . $datewarehouse . ' 10:00:00\' AND ' . '\'' . $datewarehouse . ' 11:59:59\'');
			} else if ($time_id === '3') {
				array_push($timeset, '\'' . $datewarehouse . ' 12:00:00\' AND ' . '\'' . $datewarehouse . ' 13:59:59\'');
			} else if ($time_id === '4') {
				array_push($timeset, '\'' . $datewarehouse . ' 14:00:00\' AND ' . '\'' . $datewarehouse . ' 15:59:59\'');
			} else if ($time_id === '5') {
				array_push($timeset, '\'' . $datewarehouse . ' 16:00:00\' AND ' . '\'' . $datewarehouse . ' 17:59:59\'');
			} else if ($time_id === '6') {
				array_push($timeset, '\'' . $datewarehouse . ' 18:00:00\' AND ' . '\'' . $datewarehouse . ' 19:59:59\'');
			} else if ($time_id === '7') {
				array_push($timeset, '\'' . $datewarehouse . ' 20:00:00\' AND ' . '\'' . $datewarehouse . ' 21:59:59\'');
			} else if ($time_id === '8') {
				array_push($timeset, '\'' . $datewarehouse . ' 22:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 23:59:59\'');
			} else if ($time_id === '9') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 00:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 01:59:59\'');
			} else if ($time_id === '10') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 02:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 03:59:59\'');
			} else if ($time_id === '11') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 04:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 05:59:59\'');
			} else if ($time_id === '12') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 06:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'');
			}
		}

		$rows = (new WarehouseService)->getReportSendToSVO($timeset, $location_type);

		// echo $rows; exit;

		renderView('pdf/pdf_pcr_to_svo', [
			'rows' => $rows,
			'timeset' => $timeset,
			'shift' => $shift,
			'date' => $datewarehouse
		]);
	}

	public function genwarehousePDF() // update 24/02/2017
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$datewarehouse = date('Y-m-d', strtotime($date));
		$datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$time_selected = $_POST["selecttime"];
		$timeset = [];
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}

		$batch = $_POST['batch'];
		if ($batch === 'over2020') {
			$batch = "YEAR(T.CuringDate) >= 2020";
		} else if ($batch === 'less2020') {
			$batch = "YEAR(T.CuringDate) < 2020";;
		} else {
			$batch = "T.CuringDate IS NOT NULL";
		}

		$warehouse = filter_input(INPUT_POST, "warehouse");

		if ($warehouse == "sent") {

			foreach ($time_selected as $k => $time_id) {
				if ($time_id === '13') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 08:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 08:59:59\'');
				} else if ($time_id === '14') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 09:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 09:59:59\'');
				} else if ($time_id === '15') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 10:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 10:59:59\'');
				} else if ($time_id === '16') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 11:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 11:59:59\'');
				} else if ($time_id === '17') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 12:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 12:59:59\'');
				} else if ($time_id === '18') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 13:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 13:59:59\'');
				} else if ($time_id === '19') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 14:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 14:59:59\'');
				} else if ($time_id === '20') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 15:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 15:59:59\'');
				} else if ($time_id === '21') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 16:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 16:59:59\'');
				} else if ($time_id === '22') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 17:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 17:59:59\'');
				} else if ($time_id === '23') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 18:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 18:59:59\'');
				} else if ($time_id === '24') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 19:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 19:59:59\'');
				} else if ($time_id === '25') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 20:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 20:59:59\'');
				} else if ($time_id === '26') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 21:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 21:59:59\'');
				} else if ($time_id === '27') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 22:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 22:59:59\'');
				} else if ($time_id === '28') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse . ' 23:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse . ' 23:59:59\'');
				} else if ($time_id === '29') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 00:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 00:59:59\'');
				} else if ($time_id === '30') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 01:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 01:59:59\'');
				} else if ($time_id === '31') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 02:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 02:59:59\'');
				} else if ($time_id === '32') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 03:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 03:59:59\'');
				} else if ($time_id === '33') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 04:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 04:59:59\'');
				} else if ($time_id === '34') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 05:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 05:59:59\'');
				} else if ($time_id === '35') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 06:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 06:59:59\'');
				} else if ($time_id === '36') {
					array_push($timeset, 'T.WarehouseTransReceiveDate >= \'' . $datewarehouse_nextday . ' 07:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'');
				}
			}

			$rows = (new WarehouseService)->getReportSentToWarehouse($timeset, $product_group, $pressBOI, $batch);

			// echo $rows; exit;
			// $rows = json_decode($rows);
			// echo "<pre>".print_r($rows,true)."</pre>";
			// exit;
			if ($check == 1) {
				renderView('pagemaster/pdf_warehousesent', [
					'rows' => $rows,
					'timeset' => $timeset,
					'shift' => $shift,
					'date' => $datewarehouse,
					'BOIName' => $dataBOIName
				]);
			}
			if ($check == 2) {
				renderView("pagemaster/excel_warehousesent", [
					'rows' => $rows,
					'timeset' => $timeset,
					'shift' => $shift,
					'date' => $datewarehouse,
					'BOIName' => $dataBOIName
				]);
			}
		} else if ($warehouse == "recive") {

			foreach ($time_selected as $k => $time_id) {
				if ($time_id === '13') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 08:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 08:59:59\'');
				} else if ($time_id === '14') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 09:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 09:59:59\'');
				} else if ($time_id === '15') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 10:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 10:59:59\'');
				} else if ($time_id === '16') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 11:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 11:59:59\'');
				} else if ($time_id === '17') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 12:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 12:59:59\'');
				} else if ($time_id === '18') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 13:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 13:59:59\'');
				} else if ($time_id === '19') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 14:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 14:59:59\'');
				} else if ($time_id === '20') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 15:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 15:59:59\'');
				} else if ($time_id === '21') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 16:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 16:59:59\'');
				} else if ($time_id === '22') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 17:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 17:59:59\'');
				} else if ($time_id === '23') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 18:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 18:59:59\'');
				} else if ($time_id === '24') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 19:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 19:59:59\'');
				} else if ($time_id === '25') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 20:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 20:59:59\'');
				} else if ($time_id === '26') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 21:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 21:59:59\'');
				} else if ($time_id === '27') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 22:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 22:59:59\'');
				} else if ($time_id === '28') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse . ' 23:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse . ' 23:59:59\'');
				} else if ($time_id === '29') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 00:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 00:59:59\'');
				} else if ($time_id === '30') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 01:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 01:59:59\'');
				} else if ($time_id === '31') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 02:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 02:59:59\'');
				} else if ($time_id === '32') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 03:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 03:59:59\'');
				} else if ($time_id === '33') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 04:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 04:59:59\'');
				} else if ($time_id === '34') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 05:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 05:59:59\'');
				} else if ($time_id === '35') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 06:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 06:59:59\'');
				} else if ($time_id === '36') {
					array_push($timeset, 'T.WarehouseReceiveDate >= \'' . $datewarehouse_nextday . ' 07:00:00\' AND T.WarehouseReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'');
				}
			}

			// $time  = convertforin(implode(',',$_POST["selecttime"]));
			// $counttime = count($_POST["selecttime"]);
			if (isset($_POST['selectbrand'])) {
				$brand_select = $_POST['selectbrand'];
				$brand  = '';
				foreach ($brand_select as $v) {
					$brand .= $v . ', ';
				}
				$brand = trim($brand, ', ');
				//convertforin(implode(',',$_POST["selectbrand"]));
			}

			$rows = (new WarehouseService)->getReportReceiveToWarehouse($shift, $timeset, $datewarehouse, $brand, $product_group, $pressBOI, $batch);

			// $arr = $this->report->GreentireInventoryServiceallpdfwarehouserecive($shift,$timeset,$datewarehouse,$brand);
			// var_dump($shift,$timeset,$datewarehouse,$brand);

			// $rows = json_decode($rows);
			// echo "<pre>" . print_r($rows, true) . '</pre>'; exit;

			if ($shift == "day") {
				$shift = "กลางวัน";
			} else {
				$shift = "กลางคืน";
			}

			if ($check == 1) {
				renderView('pagemaster/pdf_warehouserecive', [
					'rows' => $rows,
					'timeset' => $timeset,
					'shift' => $shift,
					'date' => date('d/m/Y', strtotime($datewarehouse)),
					'BOIName' => $dataBOIName
				]);
			} else {
				renderView('pagemaster/excel_warehouserecive', [
					'rows' => $rows,
					'timeset' => $timeset,
					'shift' => $shift,
					'date' => $datewarehouse,
					'BOIName' => $dataBOIName
				]);
			}
		}

		exit;
		// function convertforin($str){
		// 		$strploblem = "";
		// 		$a =explode(',', $str);
		// 		foreach ($a as $value) {
		// 				if($strploblem===""){
		// 						$strploblem.=$value;
		// 				}else{
		// 						$strploblem.=",".$value;
		// 				}
		// 		}
		// 		return $strploblem;
		// }


		//   	if($shift=='day') {
		//   		if ($counttime==1) {
		// 		if ($time==1) {
		// 			$timeto="08:01";	$timefrom="11:00";
		// 		}elseif ($time==2) {
		// 			$timeto="11:01";	$timefrom="14:00";
		// 		}elseif ($time==3) {
		// 			$timeto="14:01";	$timefrom="17:00";
		// 		}elseif ($time==4) {
		// 			$timeto="17:01";	$timefrom="20:00";
		// 		}
		// 		$timeshow = $timeto."-".$timefrom;
		// 	}elseif ($counttime==2) {
		// 		if ($time=='1,2') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="11:01";	$timefrom2="14:00";
		// 		}elseif ($time=='1,3') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="14:01";	$timefrom2="17:00";
		// 		}elseif ($time=='1,4') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="17:01";	$timefrom2="20:00";
		// 		}elseif ($time=='2,3') {
		// 			$timeto1="11:01";	$timefrom1="14:00";
		// 			$timeto2="14:01";	$timefrom2="17:00";
		// 		}elseif ($time=='2,4') {
		// 			$timeto1="11:01";	$timefrom1="14:00";
		// 			$timeto2="17:01";	$timefrom2="20:00";
		// 		}elseif ($time=='3,4') {
		// 			$timeto1="14:01";	$timefrom1="17:00";
		// 			$timeto2="17:01";	$timefrom2="20:00";
		// 		}
		// 		$timeshow = $timeto1."-".$timefrom1.",".$timeto2."-".$timefrom2;
		// 	}elseif ($counttime==3) {
		// 		if ($time=='1,2,3') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="11:01";	$timefrom2="14:00";
		// 			$timeto3="14:01";	$timefrom3="17:00";
		// 		}elseif ($time=='1,2,4') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="11:01";	$timefrom2="14:00";
		// 			$timeto3="17:01";	$timefrom3="20:00";
		// 		}elseif ($time=='1,3,4') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="14:01";	$timefrom2="17:00";
		// 			$timeto3="17:01";	$timefrom3="20:00";
		// 		}elseif ($time=='2,3,4') {
		// 			$timeto1="11:01";	$timefrom1="14:00";
		// 			$timeto2="14:01";	$timefrom2="17:00";
		// 			$timeto3="17:01";	$timefrom3="20:00";
		// 		}
		// 		$timeshow = $timeto1."-".$timefrom1.",".$timeto2."-".$timefrom2.",".$timeto3."-".$timefrom3;
		// 	}elseif ($counttime==4) {
		// 		if ($time=='1,2,3,4') {
		// 			$timeto1="08:01";	$timefrom1="11:00";
		// 			$timeto2="11:01";	$timefrom2="14:00";
		// 			$timeto3="14:01";	$timefrom3="17:00";
		// 			$timeto4="17:01";	$timefrom4="20:00";
		// 		}
		// 		$timeshow = $timeto1."-".$timefrom1.",".$timeto2."-".$timefrom2.",".$timeto3."-".$timefrom3.",".$timeto4."-".$timefrom4;
		// 	}
		//   	}elseif ($shift=='night') {
		//   		$timeshow = "20:01-08:00";
		//   	}

		//   	$warehouse =filter_input(INPUT_POST, "warehouse");
		//   	if ($warehouse=="sent") {
		//   		$pagewarehouse = "pdf_warehousesent";
		//   		$arr = $this->report->GreentireInventoryServiceallpdfwarehousesent($shift,$time,$counttime,$datewarehouse);
		// 	$json_decode  = json_decode($arr);
		//   	}elseif ($warehouse=="recive") {
		//   		$pagewarehouse = "pdf_warehouserecive";
		//   		$brand  = convertforin(implode(',',$_POST["selectbrand"]));
		//   		$arr = $this->report->GreentireInventoryServiceallpdfwarehouserecive($shift,$time,$counttime,$datewarehouse,$brand);
		// 	$json_decode  = json_decode($arr);

		// 	$number = count(array_filter($json_decode));
		// 	$numberall = (13-$number);
		// 	$fake_data = [
		// 		[0], //1
		// 		[0], //2
		// 		[0], //3
		// 		[0], //4
		// 		[0], //5
		// 		[0], //6
		// 		[0], //7
		// 		[0], //8
		// 		[0], //9
		// 		[0], //10
		// 		[0], //11
		// 		[0], //12
		// 		[0], //13
		// 	];
		// 	for ($i=0; $i < $numberall; $i++) {
		// 			foreach ($fake_data[$i] as $value) {
		// 				//$sorted = [];
		// 				$json_decode[] = (object) [
		// 					'Pages' =>1,
		// 					'ItemID' => '',
		// 				    'NameTH' => '',
		// 				    'QTY' => '',
		// 		            'Batch' => '',
		// 				];
		// 				$sorted = $json_decode;
		// 			}
		// 	}
		//   	}
		//   	if ($shift=='day') {
		//   		$shift = "กลางวัน";
		//   	}else{
		//   		$shift = "กลางคืน";
		//   	}
		// renderView("pagemaster/".$pagewarehouse,[
		// 	"datajson" => $json_decode,
		// 	"date" => $date,
		// 	"shift" => $shift,
		// 	"timeshow" => $timeshow,
		// 	"number" => ($number/14)
		// ]);
	}

	public function curingPress()
	{
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press_no		 = filter_input(INPUT_POST, "press_no");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date_curing = date('Y-m-d', strtotime($date_curing));

		$result = (new ReportService)->curingPress($date_curing, $press_no, $shift);
		$check_press = (new ReportService)->checkProductGroup($date_curing, $press_no, $shift);
		$presscheck = substr($press_no, 0, 1);

		// echo "<pre>" . print_r($result, true) . '</pre>'; exit;
		// echo $press_no[0] ;
		//print_r($presscheck); exit();
		if ($presscheck == "L") {
			$txthead = "ใบรายงานจำานวนยาง (PCR) ที่อบ";
		} else {
			$txthead = "ใบรายงานจำนวนยางที่อบ";
		}

		if (
			$presscheck == "A" ||
			$presscheck == "B" ||
			$presscheck == "C" ||
			$presscheck == "D" ||
			$presscheck == "E" ||
			$presscheck == "F" ||
			$presscheck == "G" ||
			$presscheck == "H" ||
			$presscheck == "K" ||
			$presscheck == "O" ||
			$presscheck == "P"
		) {
			$txtfooter = "3";
		} else {
			$txtfooter = "2";
		}
		if ($press_no[0] == 'I' || $press_no[0] == 'J')
		//if(json_decode($check_press['L']) == 'RDT' )
		{
			renderView('report/curing_press_i', [
				'L' => json_decode($result['L']),
				'R' => json_decode($result['R']),
				'shift' => $result['shift'],
				'date_curing' => $result['date_curing'],
				'weekly' => $result['weekly'],
				'press_no' => $press_no

			]);
		} else {
			renderView('report/curing_press', [
				'L' => json_decode($result['L']),
				'R' => json_decode($result['R']),
				'shift' => $result['shift'],
				'date_curing' => $result['date_curing'],
				'weekly' => $result['weekly'],
				'press_no' => $press_no,
				'txtfooter' => $txtfooter,
				'txthead' => $txthead

			]);
		}
		// renderView('report/curing_press_i');
		// renderView('report/curing_press', [
		// 	'L' => json_decode($result['L']),
		// 	'R' => json_decode($result['R']),
		// 	'shift' => $result['shift'],
		// 	'date_curing' => $result['date_curing'],
		// 	'weekly' => $result['weekly'],
		// 	'press_no' => $press_no
		// ]);
	}

	public function gencuringpressPDF()
	{
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press 		 = filter_input(INPUT_POST, "press_no");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$datecuring = date('Y-m-d', strtotime($date_curing));
		// echo $shift; exit();
		$arr = $this->report->CuringServiceallpresspdf($datecuring, $press, $shift);
		$json_decode  = json_decode($arr);
		// echo '<pre>' . print_r($json_decode) . '</pre>';  exit;
		$arrGTL = $this->report->CuringServiceallpresspdfGTL($datecuring, $press, $shift);
		$json_decodeGTL  = json_decode($arrGTL);
		$arrGTR = $this->report->CuringServiceallpresspdfGTR($datecuring, $press, $shift);
		$json_decodeGTR  = json_decode($arrGTR);
		$arrW = $this->report->CuringServiceallpresspdfweekly($datecuring, $press, $shift);
		$json_decodeW  = json_decode($arrW);
		$arrCL = $this->report->CuringServiceallpresspdfCurcodeL($datecuring, $press, $shift);
		$json_decodeCL  = json_decode($arrCL);
		$arrCR = $this->report->CuringServiceallpresspdfCurcodeR($datecuring, $press, $shift);
		$json_decodeCR  = json_decode($arrCR);
		//echo "<pre>".print_r($json_decodeGTL,true)."</pre>";
		//echo $press;
		//exit();
		// $chk = $json_decode[0]->
		renderView("pagemaster/pdf_curingpress", [
			"datecuring" => $date_curing,
			"shift" => $shift,
			"datajson" => $json_decode,
			"datajsonGTL" => $json_decodeGTL,
			"datajsonGTR" => $json_decodeGTR,
			"datajsonW" => $json_decodeW,
			"datajsonCL" => $json_decodeCL,
			"datajsonCR" => $json_decodeCR,
			"press" => $press,
			"chk" => $chk
		]);
	}


	public function buildingAx()
	{
		renderView("report/building_ax");
	}

	public function buildingAcc()
	{
		renderView("report/building_acc");
	}

	public function buildingAxPdf()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/building_ax'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}

		$arr = $this->report->BuildingServiceallpdf($datebuilding, $shift, $group, $product_group, $pressBOI);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					'Q5' => '',
					'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		$datagroup = $json_decode[0]->Description;
		renderView("report/pdf_building_ax", [
			"datajson" => $json_decode,
			"date_building" => $date_building,
			"shift" => $shift,
			"group" => $datagroup,
			"BOIName" => $dataBOIName
		]);
	}

	public function curingAx()
	{
		renderView("report/curing_ax");
	}

	public function curingAxPdf()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/curing_ax'>กลับไป</a>";
			exit;
		}

		$dateCuring = filter_input(INPUT_POST, "date_curing");
		$date_curing = Date('Y-m-d', strtotime($dateCuring));
		$shift = filter_input(INPUT_POST, "shift");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}



		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}


		$data = (new ReportService)->curingAx($date_curing, $shift, $product_group, $pressBOI);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit;


		if ($check == 1) {
			renderView("report/pdf_curing_ax", [
				"date_curing" => $date_curing,
				"shift" => $shift,
				"data" => json_decode($data),
				"BOIName" => $dataBOIName
			]);
		}
		if ($check == 2) {
			renderView("report/excel_curing_ax", [
				"date_curing" => $date_curing,
				"shift" => $shift,
				"data" => json_decode($data),
				"BOIName" => $dataBOIName
			]);
		}
	}

	public function gencureinventoryPDF()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/cure/inventory'>กลับไป</a>";
			exit;
		}

		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		$item_group = $_POST['item_group'];
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$check = filter_input(INPUT_POST, "check_type");

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = $this->convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}

		$arr = $this->report->CureInventoryServiceallpdf($product_group, $pressBOI);
		$json_decode  = json_decode($arr);
		//echo "<pre>".print_r($json_decode,true)."</pre>";
		//exit();
		if ($check == 1) {
			renderView("pagemaster/pdf_cureinventory", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		} else {

			renderView("pagemaster/pdf_cureinventoryexcel", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		}
	}

	public function genwipfinalfgPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/wipfinalfg'>กลับไป</a>";
			exit;
		}

		// date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		$item_group = $_POST['item_group'];
		$check = $_POST['check'];
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}

		$arr = $this->report->WIPServiceallpdf($product_group, $pressBOI);
		$json_decode  = json_decode($arr);

		if ($check == 1) {
			renderView("pagemaster/pdf_wipfinalfg", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		} else {
			renderView("pagemaster/excel_wipfinalfg", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		}
	}

	public function curetireMaster()
	{
		renderView("report/curetire_master");
	}

	public function curetireMasterPdf()
	{
		$all = $this->report->cureCodeMasterReport();
		renderView("report/curetire_master_pdf", [
			"data" => $all
		]);
	}

	public function curetireMasterExcel()
	{
		$all = $this->report->cureCodeMasterReport();
		renderView("report/curetire_master_excel", [
			"data" => $all
		]);
	}

	public function renderGreentireHoldUnholdAndRepair()
	{
		renderView('report/greentire_hold_unhold_repair_report');
	}

	public function renderFinalHoldUnholdAndRepair()
	{
		renderView('report/final_hold_unhold_repair_report');
	}

	public function greentireHoldUnholdAndRepair()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/greentire/hold_unhold_repair'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$date = filter_input(INPUT_POST, "_date");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));

		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		//echo $pressBOI; exit();
		$invent_trans = new InventTrans;
		$result = $invent_trans->greentireHoldUnholdAndRepair($date, $product_group, $pressBOI);
		renderView('report/pdf_greentire_hold_unhold_repair_report', [
			"result" => json_decode($result),
			"date" => $date,
			"BOIName" => $dataBOIName
		]);
	}

	public function finalHoldUnholdAndRepair()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/final/hold_unhold_repair'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$date = filter_input(INPUT_POST, "_date");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}

		$invent_trans = new InventTrans;
		$result = $invent_trans->finalHoldUnholdAndRepair($date, $product_group, $pressBOI);
		renderView('report/pdf_final_hold_unhold_repair_report', [
			"result" => json_decode($result),
			"date" => $date,
			"BOIName" => $dataBOIName
		]);
	}

	public function pdfCuringPressNew()
	{
		echo "PDF Curing Press New !";
	}

	public function buildingMachine()
	{
		renderView("report/building_machine");
	}

	public function buildingMachinePdf()
	{
		if (isset($_POST['machine'])) {
			$machine_select = $_POST['machine'];
			$machine  = '';
			foreach ($machine_select as $v) {
				$machine .= '\'' . $v . '\',';
			}
			$machine = trim($machine, ', ');
		}
		$date 	= filter_input(INPUT_POST, "date_building");
		$shift 	= filter_input(INPUT_POST, "shift");

		$data = (new ReportService)->buildingMachine($date, $shift, $machine);

    
		// $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		// 			echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . '"><br />';

		//echo "<pre>". print_r($data, true) . "</pre>";
		// $data = json_decode($data);
		// foreach ($data as $value) {

		// 	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		// 			echo '<img width="100" height="15" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . '">
		// 			<br><br>
		// 			';
		// }
		// exit();
		if ($shift == "day") {
			$shift = "กลางวัน";
		} else {
			$shift = "กลางคืน";
		}
		renderView('report/building_machine_pdf', [
			"data" => json_decode($data),
			"shift" => $shift,
			"date" => $date
		]);
	}

	public function LoadingPDF($pickingListId, $orderId, $createDate, $custName, $check)
	{
		$custName = urldecode($custName);
		$data = (new ReportService)->Loading($pickingListId, $orderId, $createDate);
		$dataexport = (new ReportService)->Loadingexport($pickingListId, $orderId, $createDate);
		$dataloading = json_decode($data);
		$dataloadingexport = json_decode($dataexport);

		if ($check == 1) {
			renderView('report/loading_pdf', [
				"pickingListId" => $pickingListId,
				"orderId" 		=> $orderId,
				"createDate" 	=> $createDate,
				"custName" 		=> $custName,
				"dataloading"	=> $dataloading
			]);
		}
		if ($check == 2) {
			renderView('report/loading_excel', [
				"pickingListId" => $pickingListId,
				"orderId" 		=> $orderId,
				"createDate" 	=> $createDate,
				"custName" 		=> $custName,
				"dataloading"	=> $dataloadingexport
			]);
		}
	}

	public function pagePCRtoSVO()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("report/report_pcr_to_svo");
	}

	public function genFinalPDF()
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$time_selected = $_POST["selecttime"];
		$datewarehouse = date('Y-m-d', strtotime($date));
		$datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$timeset = [];
		$check = filter_input(INPUT_POST, "check_type");
		$item_group = $_POST['item_group'];
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		$warehouse = filter_input(INPUT_POST, "warehouse");
		// if ($shift == 'day') {
		// 	$timeset = 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 08:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 16:59:59\'';
		// } else {
		// 	$timeset = 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 20:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'';
		// }
		foreach ($time_selected as $k => $time_id) {
			if ($time_id === '13') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 08:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 08:59:59\'');
			} else if ($time_id === '14') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 09:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 09:59:59\'');
			} else if ($time_id === '15') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 10:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 10:59:59\'');
			} else if ($time_id === '16') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 11:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 11:59:59\'');
			} else if ($time_id === '17') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 12:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 12:59:59\'');
			} else if ($time_id === '18') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 13:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 13:59:59\'');
			} else if ($time_id === '19') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 14:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 14:59:59\'');
			} else if ($time_id === '20') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 15:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 15:59:59\'');
			} else if ($time_id === '21') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 16:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 16:59:59\'');
			} else if ($time_id === '22') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 17:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 17:59:59\'');
			} else if ($time_id === '23') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 18:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 18:59:59\'');
			} else if ($time_id === '24') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 19:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 19:59:59\'');
			} else if ($time_id === '25') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 20:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 20:59:59\'');
			} else if ($time_id === '26') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 21:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 21:59:59\'');
			} else if ($time_id === '27') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 22:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 22:59:59\'');
			} else if ($time_id === '28') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse . ' 23:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse . ' 23:59:59\'');
			} else if ($time_id === '29') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 00:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 00:59:59\'');
			} else if ($time_id === '30') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 01:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 01:59:59\'');
			} else if ($time_id === '31') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 02:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 02:59:59\'');
			} else if ($time_id === '32') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 03:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 03:59:59\'');
			} else if ($time_id === '33') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 04:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 04:59:59\'');
			} else if ($time_id === '34') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 05:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 05:59:59\'');
			} else if ($time_id === '35') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 06:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 06:59:59\'');
			} else if ($time_id === '36') {
				array_push($timeset, 'T.FinalReceiveDate >= \'' . $datewarehouse_nextday . ' 07:00:00\' AND T.FinalReceiveDate <= ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'');
			}
			}
			
			$rows = (new FinalService)->getReportFinal($timeset, $product_group, $pressBOI,$datewarehouse);
			
				if ($check == 1) {
					renderView('pagemaster/pdf_finalrecive', [
						'rows' => $rows,
						'timeset' => $timeset,
						'shift' => $shift,
						'date' => $datewarehouse,
						'BOIName' => $dataBOIName
					]);
				}

				if ($check == 2) {
					renderView("pagemaster/excel_finalsend", [
						'rows' => $rows,
						'timeset' => $timeset,
						'shift' => $shift,
						'date' => $datewarehouse,
						'BOIName' => $dataBOIName
					]);
				
		}
		exit;
	}
	

	public function convertforinselect($str)
	{
		$strploblem = "";
		$a = explode(',', $str);
		foreach ($a as $value) {
			if ($strploblem === "") {
				$strploblem .= $value;
			} else {
				$strploblem .= "," . $value;
			}
		}
		return $strploblem;
	}
	public function curingAxSend()
	{
		renderView("report/curing_axsend");
	}
	public function curingAxSendPdf()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/curing_ax'>กลับไป</a>";
			exit;
		}

		$dateCuring = filter_input(INPUT_POST, "date_curing");
		$date_curing = Date('Y-m-d', strtotime($dateCuring));
		$date_curing_nextday = date('Y-m-d', strtotime($date_curing . ' +1 days'));
		$shift = filter_input(INPUT_POST, "shift");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		//$timeset = '';
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}



		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$batch = $_POST['batch'];
		if ($batch === 'over2020') {
			$batch = "YEAR(T.CuringDate) >= 2020";
		} else if ($batch === 'less2020') {
			$batch = "YEAR(T.CuringDate) < 2020";;
		} else {
			$batch = "T.CuringDate IS NOT NULL";
		}

		if ($shift == 'day') {
			$timeset = 'T.WarehouseTransReceiveDate >= \'' . $date_curing . ' 08:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $date_curing . ' 19:59:59\'';
		} else {
			$timeset = 'T.WarehouseTransReceiveDate >= \'' . $date_curing . ' 20:00:00\' AND T.WarehouseTransReceiveDate <= ' . '\'' . $date_curing_nextday . ' 07:59:59\'';
		}

		$data = (new WarehouseService)->getReportCurngaxSentToWarehouse($date_curing, $timeset, $product_group, $pressBOI, $shift, $batch);
		// echo "<pre>";
		// var_dump($data); exit;
		// echo "</pre>";


		if ($check == 1) {
			renderView("report/pdf_curingsend_ax", [
				"date_curing" => $date_curing,
				"shift" => $shift,
				"data" => json_decode($data),
				"BOIName" => $dataBOIName
			]);
		}
		if ($check == 2) {
			renderView("report/excel_curingsend_ax", [
				"date_curing" => $date_curing,
				"shift" => $shift,
				"data" => json_decode($data),
				"BOIName" => $dataBOIName
			]);
		}
	}
	public function greentireInspectionReport()
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$datewarehouse = date('Y-m-d', strtotime($date));
		$datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$timeset = [];
		$check = filter_input(INPUT_POST, "check_type");
		$item_group = $_POST['item_group'];
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'SM0908';
		} else {
			$product_group = 'SM0907';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		$warehouse = filter_input(INPUT_POST, "warehouse");
		if ($shift == 'day') {
			$timeset = 'I.GT_InspectionDate >= \'' . $datewarehouse . ' 08:00:00\' AND I.GT_InspectionDate <= ' . '\'' . $datewarehouse . ' 19:59:59\'';
		} else {
			$timeset = 'I.GT_InspectionDate >= \'' . $datewarehouse . ' 20:00:00\' AND I.GT_InspectionDate <= ' . '\'' . $datewarehouse_nextday . ' 07:59:59\'';
		}
		$rows = $this->Greentire->greentireInspectionReport($timeset, $product_group, $pressBOI);
		if ($check == 1) {
			renderView('pagemaster/pdf_greentireInspection', [
				'rows' => $rows,
				'timeset' => $timeset,
				'shift' => $shift,
				'date' => $datewarehouse,
				'BOIName' => $dataBOIName
			]);
		}
		if ($check == 2) {
			renderView("pagemaster/exel_greentireInspection", [
				'rows' => $rows,
				'timeset' => $timeset,
				'shift' => $shift,
				'date' => $datewarehouse,
				'BOIName' => $dataBOIName
			]);
		}
	}

	public function genDeductPDF()
	{

		$month = filter_input(INPUT_POST, "month");
		$mm = substr($month, 0, 2);
		$yy = substr($month, 3);
		$machine = $_POST["selectMac"]["0"];
		$userid = filter_input(INPUT_POST, "UserId");
		$type = "";

		if ($machine === null) {
			$machineH = "ALL";
			$type = "";
			$machine = "";
		} elseif ($machine === '- ALL TBR -') {
			$machineH = "ALL TBR";
			$type = "TBR";
			$machine = "";
		} elseif ($machine === '- ALL PCR -') {
			$machineH = "ALL PCR";
			$type = "PCR";
			$machine = "";
		} else {
			$machineH = $machine;
			$type = "";
			$machine = $machine;
		}
		// echo "<pre>".print_r($mm,true)."</pre>";
		// echo "<pre>".print_r($machine,true)."</pre>";
		// echo "<pre>".print_r($userid,true)."</pre>";
		// exit();

		$rpt = new ReportService;
		$report = $rpt->DeductServicepdf($mm, $type, $machine, $userid, $yy);

		// echo "<pre>".print_r($report ,true)."</pre>";
		// exit();

		$sorted = [];

		foreach ($report as $r) {
			$sorted[] = [
				// 'rowspan' => 3,
				'EmployeeID' => $r['EmployeeID'],
				'Name' => $r['Name'],
				'DeductDate' => $r['DeductDate'],
				'Machine' => $r['Machine'],
				'Charge' => $r['Charge'],
				'Remark' => $r['Remark'],
				'Shift' => $r['Shift']
			];
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();


		renderView("pagemaster/pdf_deduct", [
			"data" => $sorted,
			"month" => $month,
			"machine" => $machineH
		]);
	}

	public function genRateMonthlyPDF()
	{
		
		$month = filter_input(INPUT_POST, "month");
		$check = filter_input(INPUT_POST, "check_type");
		$item_group = $_POST['item_group'];

		$machine_type = 'TBR';
		if ($item_group === 'tbr') {
			$machine_type = 'TBR';
		} else {
			$machine_type = 'PCR';
		}

		$export_date = date("d-m-Y H:i:s");
		$mm = substr($month, 0, 2);
		$yy = substr($month, 3);

		if (($mm - 1) === 0) {
			$mm_s = 12;
			$yy_s = $yy - 1;
		} else {
			$mm_s = str_pad(($mm - 1), 2, '0', STR_PAD_LEFT);

			$yy_s = $yy;
		}

		$month_year = [
			"01" => "มกราคม",
			"02" => "กุมภาพันธ์",
			"03" => "มีนาคม",
			"04" => "เมษายน",
			"05" => "พฤษภาคม",
			"06" => "มิถุนายน",
			"07" => "กรกฎาคม",
			"08" => "สิงหาคม",
			"09" => "กันยายน",
			"10" => "ตุลาคม",
			"11" => "พฤศจิกายน",
			"12" => "ธันวาคม",
		];

		$month_now = $month_year[$mm];
		$month_part = $month_year[$mm_s];
		$year_now = $yy + 543;
		$year_part = $yy_s + 543;


		$date_start = $yy_s . "-" . $mm_s . "-21 08:00:00";
		$date_end = $yy . "-" . $mm . "-21 07:59:59";

		// echo "<pre>".print_r($month_now,true)."</pre>";
		// echo "<pre>".print_r($month_part,true)."</pre>";
		// echo "<pre>".print_r($date_start,true)."</pre>";
		// echo "<pre>".print_r($date_end,true)."</pre>";
		// exit();

		$rpt = new ReportService;

		// $report = $rpt->RateMonthlyServicepdf($date_start, $date_end, $machine_type);
		if($machine_type == 'TBR'){
			$report = $rpt->RateMonthlyServicepdf($date_start, $date_end, $machine_type);
		}else{
			$report = $rpt->RateMonthlyServicepdf_PCR($date_start, $date_end, $machine_type);
		}
		



		// echo "<pre>".print_r($report,true)."</pre>";
		// exit();
		$sorted = [];

		// $total_charge,$total_d1
		$total_charge = 0;
		$d21 = 0;
		$d22 = 0;
		$d23 = 0;
		$d24 = 0;
		$d25 = 0;
		$d26 = 0;
		$d27 = 0;
		$d28 = 0;
		$d29 = 0;
		$d30 = 0;
		$d31 = 0;
		$d01 = 0;
		$d02 = 0;
		$d03 = 0;
		$d04 = 0;
		$d05 = 0;
		$d06 = 0;
		$d07 = 0;
		$d08 = 0;
		$d09 = 0;
		$d10 = 0;
		$d11 = 0;
		$d12 = 0;
		$d13 = 0;
		$d14 = 0;
		$d15 = 0;
		$d16 = 0;
		$d17 = 0;
		$d18 = 0;
		$d19 = 0;
		$d20 = 0;
		$sum_total = 0;
		foreach ($report as $r) {
			$total_charge += $r['Charge'];
			$sum_total += $r['Total'];
			$d21 += $r['D21'];
			$d22 += $r['D22'];
			$d23 += $r['D23'];
			$d24 += $r['D24'];
			$d25 += $r['D25'];
			$d26 += $r['D26'];
			$d27 += $r['D27'];
			$d28 += $r['D28'];
			$d29 += $r['D29'];
			$d30 += $r['D30'];
			$d31 += $r['D31'];
			$d01 += $r['D01'];
			$d02 += $r['D02'];
			$d03 += $r['D03'];
			$d04 += $r['D04'];
			$d05 += $r['D05'];
			$d06 += $r['D06'];
			$d07 += $r['D07'];
			$d08 += $r['D08'];
			$d09 += $r['D09'];
			$d10 += $r['D10'];
			$d11 += $r['D11'];
			$d12 += $r['D12'];
			$d13 += $r['D13'];
			$d14 += $r['D14'];
			$d15 += $r['D15'];
			$d16 += $r['D16'];
			$d17 += $r['D17'];
			$d18 += $r['D18'];
			$d19 += $r['D19'];
			$d20 += $r['D20'];

			if ($r['D21'] === 0) {
				$r['D21'] = '';
			} else {
				$r['D21'] = $r['D21'];
			}
			if ($r['D22'] === 0) {
				$r['D22'] = '';
			} else {
				$r['D22'] = $r['D22'];
			}
			if ($r['D23'] === 0) {
				$r['D23'] = '';
			} else {
				$r['D23'] = $r['D23'];
			}
			if ($r['D24'] === 0) {
				$r['D24'] = '';
			} else {
				$r['D24'] = $r['D24'];
			}
			if ($r['D25'] === 0) {
				$r['D25'] = '';
			} else {
				$r['D25'] = $r['D25'];
			}
			if ($r['D26'] === 0) {
				$r['D26'] = '';
			} else {
				$r['D26'] = $r['D26'];
			}
			if ($r['D27'] === 0) {
				$r['D27'] = '';
			} else {
				$r['D27'] = $r['D27'];
			}
			if ($r['D28'] === 0) {
				$r['D28'] = '';
			} else {
				$r['D28'] = $r['D28'];
			}
			if ($r['D29'] === 0) {
				$r['D29'] = '';
			} else {
				$r['D29'] = $r['D29'];
			}
			if ($r['D30'] === 0) {
				$r['D30'] = '';
			} else {
				$r['D30'] = $r['D30'];
			}
			if ($r['D31'] === 0) {
				$r['D31'] = '';
			} else {
				$r['D31'] = $r['D31'];
			}
			if ($r['D01'] === 0) {
				$r['D01'] = '';
			} else {
				$r['D01'] = $r['D01'];
			}
			if ($r['D02'] === 0) {
				$r['D02'] = '';
			} else {
				$r['D02'] = $r['D02'];
			}
			if ($r['D03'] === 0) {
				$r['D03'] = '';
			} else {
				$r['D03'] = $r['D03'];
			}
			if ($r['D04'] === 0) {
				$r['D04'] = '';
			} else {
				$r['D04'] = $r['D04'];
			}
			if ($r['D05'] === 0) {
				$r['D05'] = '';
			} else {
				$r['D05'] = $r['D05'];
			}
			if ($r['D06'] === 0) {
				$r['D06'] = '';
			} else {
				$r['D06'] = $r['D06'];
			}
			if ($r['D07'] === 0) {
				$r['D07'] = '';
			} else {
				$r['D07'] = $r['D07'];
			}
			if ($r['D08'] === 0) {
				$r['D08'] = '';
			} else {
				$r['D08'] = $r['D08'];
			}
			if ($r['D09'] === 0) {
				$r['D09'] = '';
			} else {
				$r['D09'] = $r['D09'];
			}
			if ($r['D10'] === 0) {
				$r['D10'] = '';
			} else {
				$r['D10'] = $r['D10'];
			}
			if ($r['D11'] === 0) {
				$r['D11'] = '';
			} else {
				$r['D11'] = $r['D11'];
			}
			if ($r['D12'] === 0) {
				$r['D12'] = '';
			} else {
				$r['D12'] = $r['D12'];
			}
			if ($r['D13'] === 0) {
				$r['D13'] = '';
			} else {
				$r['D13'] = $r['D13'];
			}
			if ($r['D14'] === 0) {
				$r['D14'] = '';
			} else {
				$r['D14'] = $r['D14'];
			}
			if ($r['D15'] === 0) {
				$r['D15'] = '';
			} else {
				$r['D15'] = $r['D15'];
			}
			if ($r['D16'] === 0) {
				$r['D16'] = '';
			} else {
				$r['D16'] = $r['D16'];
			}
			if ($r['D17'] === 0) {
				$r['D17'] = '';
			} else {
				$r['D17'] = $r['D17'];
			}
			if ($r['D18'] === 0) {
				$r['D18'] = '';
			} else {
				$r['D18'] = $r['D18'];
			}
			if ($r['D19'] === 0) {
				$r['D19'] = '';
			} else {
				$r['D19'] = $r['D19'];
			}
			if ($r['D20'] === 0) {
				$r['D20'] = '';
			} else {
				$r['D20'] = $r['D20'];
			}
			if ($r['Charge'] === 0) {
				$r['Charge'] = '';
			} else {
				$r['Charge'] = $r['Charge'];
			}

			$sorted[] = [
				// 'rowspan' => 3,
				'CreateBy' => $r['CreateBy'],
				'EmployeeID' => $r['EmployeeID'],
				'Name' => $r['Name'],
				'Location' => $r['Location'],
				'Charge' => $r['Charge'],
				'D21' => $r['D21'],
				'D22' => $r['D22'],
				'D23' => $r['D23'],
				'D24' => $r['D24'],
				'D25' => $r['D25'],
				'D26' => $r['D26'],
				'D27' => $r['D27'],
				'D28' => $r['D28'],
				'D29' => $r['D29'],
				'D30' => $r['D30'],
				'D31' => $r['D31'],
				'D01' => $r['D01'],
				'D02' => $r['D02'],
				'D03' => $r['D03'],
				'D04' => $r['D04'],
				'D05' => $r['D05'],
				'D06' => $r['D06'],
				'D07' => $r['D07'],
				'D08' => $r['D08'],
				'D09' => $r['D09'],
				'D10' => $r['D10'],
				'D11' => $r['D11'],
				'D12' => $r['D12'],
				'D13' => $r['D13'],
				'D14' => $r['D14'],
				'D15' => $r['D15'],
				'D16' => $r['D16'],
				'D17' => $r['D17'],
				'D18' => $r['D18'],
				'D19' => $r['D19'],
				'D20' => $r['D20'],
				'Total' => number_format($r['Total'])

			];
		}

		// echo "<pre>".print_r($total_charge,true)."</pre>";
		// exit();
		if ($check == 1) {
			renderView("pagemaster/pdf_ratemonth", [
				"data" => $sorted,
				"export_date" => $export_date,
				"month_now" => $month_now,
				"month_part" => $month_part,
				"year_now" => $year_now,
				"year_part" => $year_part,
				"machine_type" => $machine_type,
				"total_charge" => number_format($total_charge),
				"sum_total" => number_format($sum_total),
				"d21" => number_format($d21),
				"d22" => number_format($d22),
				"d23" => number_format($d23),
				"d24" => number_format($d24),
				"d25" => number_format($d25),
				"d26" => number_format($d26),
				"d27" => number_format($d27),
				"d28" => number_format($d28),
				"d29" => number_format($d29),
				"d30" => number_format($d30),
				"d31" => number_format($d31),
				"d01" => number_format($d01),
				"d02" => number_format($d02),
				"d03" => number_format($d03),
				"d04" => number_format($d04),
				"d05" => number_format($d05),
				"d06" => number_format($d06),
				"d07" => number_format($d07),
				"d08" => number_format($d08),
				"d09" => number_format($d09),
				"d10" => number_format($d10),
				"d11" => number_format($d11),
				"d12" => number_format($d12),
				"d13" => number_format($d13),
				"d14" => number_format($d14),
				"d15" => number_format($d15),
				"d16" => number_format($d16),
				"d17" => number_format($d17),
				"d18" => number_format($d18),
				"d19" => number_format($d19),
				"d20" => number_format($d20)
			]);
		} else {
			renderView("pagemaster/excel_ratebuild_month", [
				"data" => $sorted,
				"export_date" => $export_date,
				"month_now" => $month_now,
				"month_part" => $month_part,
				"year_now" => $year_now,
				"year_part" => $year_part,
				"machine_type" => $machine_type,
				"total_charge" => number_format($total_charge),
				"sum_total" => number_format($sum_total),
				"d21" => number_format($d21),
				"d22" => number_format($d22),
				"d23" => number_format($d23),
				"d24" => number_format($d24),
				"d25" => number_format($d25),
				"d26" => number_format($d26),
				"d27" => number_format($d27),
				"d28" => number_format($d28),
				"d29" => number_format($d29),
				"d30" => number_format($d30),
				"d31" => number_format($d31),
				"d01" => number_format($d01),
				"d02" => number_format($d02),
				"d03" => number_format($d03),
				"d04" => number_format($d04),
				"d05" => number_format($d05),
				"d06" => number_format($d06),
				"d07" => number_format($d07),
				"d08" => number_format($d08),
				"d09" => number_format($d09),
				"d10" => number_format($d10),
				"d11" => number_format($d11),
				"d12" => number_format($d12),
				"d13" => number_format($d13),
				"d14" => number_format($d14),
				"d15" => number_format($d15),
				"d16" => number_format($d16),
				"d17" => number_format($d17),
				"d18" => number_format($d18),
				"d19" => number_format($d19),
				"d20" => number_format($d20)
			]);
		}
	}

	public function genRatebuildingPDF_V2()
	{

		$date_rate = filter_input(INPUT_POST, "date_rate");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_rate));
		$check =  filter_input(INPUT_POST, "check_type");
		$export_date = date("d-m-Y H:i:s");

		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		$item_group = $_POST['item_group'];

		$group = 'tbr';
		if ($item_group === 'tbr') {
			$group = 'tbr';
		} elseif ($item_group === 'pcr_n') {
			$group = 'pcr_n';
		} else {
			$group = 'pcr';
		}

		$rpt = new ReportService;

		$getmachine = $rpt->getMachineByGROUP($group);
		$report = $rpt->RateBuildServicepdf_GROUP($tstart, $tend, $group);
		$check_allrows = $rpt->countUser_ALLGROUP($tstart, $tend, $group);
		// echo "<pre>".print_r($check_allrows,true)."</pre>";
		// exit();
		if ($check_allrows === 0) {

			$sorted[] = [
				'rowspan' => 2,
				'colspan' => 13
			];
		} else {
			$sorted = [];
			foreach ($getmachine as $key => $value) {
				// echo $value["Machine"]." ".$value["Type"] ."**".$rpt->countUserByMachine($tstart, $tend, $group, $value['Machine']);
				// echo "<br>";
				$check_rows = $rpt->countUserByMachine($tstart, $tend, $group, $value['Machine']);

				if ($value['Type'] === "TBR") {
					if ($check_rows === 0) {
						for ($i = 0; $i < 4; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 1) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						for ($i = 0; $i < 3; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 2) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						for ($i = 0; $i < 2; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 3) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 4,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'SCH' => "",
							'Act' => "",
							'P1' => "",
							'P2' => "",
							'P3' => "",
							'Charge' => "",
							'Total' => "",
							'Total_Diff' => "",
							'Sum_Total' => "",
							'Qty1' => "",
							'Qty2' => "",
							'Qty3' => ""
						];
					}
					if ($check_rows >= 4) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => $check_rows,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
					}
				} else if ($value['Type'] === "PCR") {
					if ($check_rows === 0) {
						for ($i = 0; $i < 3; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 1) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 3,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						for ($i = 0; $i < 2; $i++) {
							$sorted[] = [
								'Machine' => $value['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Sum_Total' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => ""
							];
						}
					}
					if ($check_rows === 2) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => 3,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
						$sorted[] = [
							'Machine' => $value['Machine'],
							'rowspan' => 3,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'SCH' => "",
							'Act' => "",
							'P1' => "",
							'P2' => "",
							'P3' => "",
							'Charge' => "",
							'Total' => "",
							'Total_Diff' => "",
							'Sum_Total' => "",
							'Qty1' => "",
							'Qty2' => "",
							'Qty3' => ""
						];
					}
					if ($check_rows >= 3) {
						foreach ($report as $r) {
							if ($r['Machine'] === $value['Machine']) {
								$sorted[] = [
									'Machine' => $r['Machine'],
									'rowspan' => $check_rows,
									'EmployeeID' => $r['EmployeeID'],
									'Name' => $r['Name'],
									'BuildType' => $r['BuildType'],
									'SCH' => $r['SCH'],
									'Act' => $r['Act'],
									'P1' => $r['P1'],
									'P2' => $r['P2'],
									'P3' => $r['P3'],
									'Charge' => $r['Charge'],
									'Total' => $r['Total'],
									'Total_Diff' => $r['Total_Diff'],
									'Sum_Total' => $r['Sum_Total'],
									'Qty1' => $r['Qty1'],
									'Qty2' => $r['Qty2'],
									'Qty3' => $r['Qty3']
								];
							}
						}
					}
				}
			}
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();
		if ($check == 1) {
			renderView("pagemaster/pdf_ratebuild", [
				"data" => $sorted,
				"date" => $date_rate,
				"shift" => $shift_th,
				"export_date" => $export_date
			]);
		} else {
			renderView("pagemaster/excel_ratebuild", [
				"data" => $sorted,
				"date" => $date_rate,
				"shift" => $shift_th,
				"export_date" => $export_date
			]);
		}
	}

	public function genRatebuildingPDF_V3()
	{

		$date_rate = filter_input(INPUT_POST, "date_rate");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_rate));

		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		$item_group = $_POST['item_group'];

		$group = 'tbr';
		if ($item_group === 'tbr') {
			$group = 'tbr';
		} elseif ($item_group === 'pcr_n') {
			$group = 'pcr_n';
		} else {
			$group = 'pcr';
		}

		$rpt = new ReportService;
		$check_allrows = $rpt->countUser_ALLGROUP($tstart, $tend, $group);

		if ($check_allrows === 0) {

			$sorted[] = [
				'rowspan' => 2,
				'colspan' => 13
			];
		} else {
			$getmachine = $rpt->getMachineByGROUP($group);
			$report = $rpt->RateBuildServicepdf_GROUP($tstart, $tend, $group);

			foreach ($getmachine as $key => $mac) {

				$check_rows = $rpt->countUserByMachine($tstart, $tend, $group, $mac['Machine']);
				if ($mac["Type"] === 'TBR') {
					if ($check_rows === 0) {
						// echo "Test0";
						for ($i = 0; $i < 4; $i++) {
							$sorted[] = [
								'Machine' => $mac['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => "",
							];
						}

						$sorted2[] = [
							'Machine' => $mac['Machine'],
							'Sum_Total' => "",
						];
					} else if ($check_rows === 1) {
						$total = 0;
						foreach ($report as $re) {
							if ($re["Machine"] === $mac["Machine"]) {
								$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$total_mac = 0;
								$total_diff = 0;

								$qty1 = 0;
								$price1 = 0;
								$qty2 = 0;
								$price2 = 0;
								$qty3 = 0;
								$price3 = 0;
								$formula = 0;
								$payment = 0;
								$headAct1 = '';
								$headAct2 = '';
								$headAct3 = '';
								$count_r = 0;

								foreach ($ratemaster as $key => $m) {
									$formula = $m["Formula"];
									$payment = $m["Payment"];

									if ($m["SeqID"] == 1) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty1 = $m["QtyMin"];
											$price1 = $m["Price"];
										}
										$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

										$total_mac += floor($price1);
									} else if ($m["SeqID"] == 2) {
										if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
											$qty2 = $m["QtyMin"];
											$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
										}
										if ($cc > 2) {
											$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
										} else if ($cc == 2) {
											$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
										}

										$total_mac += floor($price2);
									} else if ($m["SeqID"] == 3) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty3 = $m["QtyMin"];
											$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										}

										$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

										$total_mac += floor($price3);
									}
								}

								if ($total_mac == 0) {
									$total_diff = 0;
									$total += 0;
								} else {
									$total_diff = floor($total_mac) - $re['Charge'];
									$total += (floor($total_mac) - $re['Charge']);
								}


								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $re['EmployeeID'],
									'Name' => $re['Name'],
									'BuildType' => $re['BuildType'],
									'SCH' => $re['SCH'],
									'Act' => $re['Act'],
									'P1' => floor($price1),
									'P2' => floor($price2),
									'P3' => floor($price3),
									'Charge' => $re['Charge'],
									'Total' => floor($total_mac),
									'Total_Diff' => floor($total_diff),
									'Qty1' => $headAct1,
									'Qty2' => $headAct2,
									'Qty3' => $headAct3
								];
							}
						}

						$sorted2[] = [
							'Machine' => $mac['Machine'],
							'Sum_Total' => floor($total),
						];

						for ($i = 0; $i < 3; $i++) {
							$sorted[] = [
								'Machine' => $mac['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => "",
							];
						}
					} else if ($check_rows === 2) {
						$total = 0;
						foreach ($report as $re) {
							if ($re["Machine"] === $mac["Machine"]) {
								$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$total_mac = 0;
								$total_diff = 0;

								$qty1 = 0;
								$price1 = 0;
								$qty2 = 0;
								$price2 = 0;
								$qty3 = 0;
								$price3 = 0;
								$formula = 0;
								$payment = 0;
								$headAct1 = '';
								$headAct2 = '';
								$headAct3 = '';
								$count_r = 0;

								foreach ($ratemaster as $key => $m) {
									$formula = $m["Formula"];
									$payment = $m["Payment"];

									if ($m["SeqID"] == 1) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty1 = $m["QtyMin"];
											$price1 = $m["Price"];
										}
										$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

										$total_mac += floor($price1);
									} else if ($m["SeqID"] == 2) {
										if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
											$qty2 = $m["QtyMin"];
											$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
										}
										if ($cc > 2) {
											$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
										} else if ($cc == 2) {
											$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
										}

										$total_mac += floor($price2);
									} else if ($m["SeqID"] == 3) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty3 = $m["QtyMin"];
											$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										}

										$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

										$total_mac += floor($price3);
									}
								}

								if ($total_mac == 0) {
									$total_diff = 0;
									$total += 0;
								} else {
									$total_diff = floor($total_mac) - $re['Charge'];
									$total += (floor($total_mac) - $re['Charge']);
								}


								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $re['EmployeeID'],
									'Name' => $re['Name'],
									'BuildType' => $re['BuildType'],
									'SCH' => $re['SCH'],
									'Act' => $re['Act'],
									'P1' => floor($price1),
									'P2' => floor($price2),
									'P3' => floor($price3),
									'Charge' => $re['Charge'],
									'Total' => floor($total_mac),
									'Total_Diff' => floor($total_diff),
									'Qty1' => $headAct1,
									'Qty2' => $headAct2,
									'Qty3' => $headAct3
								];
							}
						}

						$sorted2[] = [
							'Machine' => $mac['Machine'],
							'Sum_Total' => floor($total),
						];

						for ($i = 0; $i < 2; $i++) {
							$sorted[] = [
								'Machine' => $mac['Machine'],
								'rowspan' => 4,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => "",
							];
						}
					} else if ($check_rows === 3) {
						$total = 0;
						foreach ($report as $re) {
							if ($re["Machine"] === $mac["Machine"]) {
								$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$total_mac = 0;
								$total_diff = 0;

								$qty1 = 0;
								$price1 = 0;
								$qty2 = 0;
								$price2 = 0;
								$qty3 = 0;
								$price3 = 0;
								$formula = 0;
								$payment = 0;
								$headAct1 = '';
								$headAct2 = '';
								$headAct3 = '';
								$count_r = 0;

								foreach ($ratemaster as $key => $m) {
									$formula = $m["Formula"];
									$payment = $m["Payment"];

									if ($m["SeqID"] == 1) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty1 = $m["QtyMin"];
											$price1 = $m["Price"];
										}
										$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

										$total_mac += floor($price1);
									} else if ($m["SeqID"] == 2) {
										if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										}
										//แก้
										else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
											$qty2 = $m["QtyMin"];
											$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
										}
										if ($cc > 2) {
											$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
										} else if ($cc == 2) {
											$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
										}

										$total_mac += floor($price2);
									} else if ($m["SeqID"] == 3) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty3 = $m["QtyMin"];
											$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										}

										$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

										$total_mac += floor($price3);
									}
								}

								if ($total_mac == 0) {
									$total_diff = 0;
									$total += 0;
								} else {
									$total_diff = floor($total_mac) - $re['Charge'];
									$total += (floor($total_mac) - $re['Charge']);
								}


								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 4,
									'EmployeeID' => $re['EmployeeID'],
									'Name' => $re['Name'],
									'BuildType' => $re['BuildType'],
									'SCH' => $re['SCH'],
									'Act' => $re['Act'],
									'P1' => floor($price1),
									'P2' => floor($price2),
									'P3' => floor($price3),
									'Charge' => $re['Charge'],
									'Total' => floor($total_mac),
									'Total_Diff' => floor($total_diff),
									'Qty1' => $headAct1,
									'Qty2' => $headAct2,
									'Qty3' => $headAct3
								];
							}
						}

						$sorted2[] = [
							'Machine' => $mac['Machine'],
							'Sum_Total' => floor($total),
						];

						$sorted[] = [
							'Machine' => $mac['Machine'],
							'rowspan' => 4,
							'EmployeeID' => "",
							'Name' => "",
							'BuildType' => "",
							'SCH' => "",
							'Act' => "",
							'P1' => "",
							'P2' => "",
							'P3' => "",
							'Charge' => "",
							'Total' => "",
							'Total_Diff' => "",
							'Qty1' => "",
							'Qty2' => "",
							'Qty3' => "",
						];
					} else if ($check_rows >= 4) {

						$total = 0;
						foreach ($report as $re) {
							if ($re["Machine"] === $mac["Machine"]) {
								$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
								$total_mac = 0;
								$total_diff = 0;

								$qty1 = 0;
								$price1 = 0;
								$qty2 = 0;
								$price2 = 0;
								$qty3 = 0;
								$price3 = 0;
								$formula = 0;
								$payment = 0;
								$headAct1 = '';
								$headAct2 = '';
								$headAct3 = '';
								$count_r = 0;

								foreach ($ratemaster as $key => $m) {
									$formula = $m["Formula"];
									$payment = $m["Payment"];

									if ($m["SeqID"] == 1) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty1 = $m["QtyMin"];
											$price1 = $m["Price"];
										}
										$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

										$total_mac += floor($price1);
									} else if ($m["SeqID"] == 2) {
										if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
											$qty2 = $m["QtyMin"];
											$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
											$qty2 = $m["QtyMin"];
											$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
										}
										if ($cc > 2) {
											$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
										} else if ($cc == 2) {
											$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
										}

										$total_mac += floor($price2);
									} else if ($m["SeqID"] == 3) {
										if ($re["Act"] >= $m["QtyMin"]) {
											$qty3 = $m["QtyMin"];
											$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
										}

										$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

										$total_mac += floor($price3);
									}
								}

								if ($total_mac == 0) {
									$total_diff = 0;
									$total += 0;
								} else {
									$total_diff = floor($total_mac) - $re['Charge'];
									$total += (floor($total_mac) - $re['Charge']);
								}


								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => $check_rows,
									'EmployeeID' => $re['EmployeeID'],
									'Name' => $re['Name'],
									'BuildType' => $re['BuildType'],
									'SCH' => $re['SCH'],
									'Act' => $re['Act'],
									'P1' => floor($price1),
									'P2' => floor($price2),
									'P3' => floor($price3),
									'Charge' => $re['Charge'],
									'Total' => floor($total_mac),
									'Total_Diff' => floor($total_diff),
									'Qty1' => $headAct1,
									'Qty2' => $headAct2,
									'Qty3' => $headAct3
								];
							}
						}

						$sorted2[] = [
							'Machine' => $mac['Machine'],
							'Sum_Total' => floor($total),
						];
					}
				} else {
					// getMachinePLY
					$checkMacPLY = $rpt->getMachinePLY($mac['Machine']);
					if ($checkMacPLY != 0) {
						if ($check_rows === 0) {
							for ($i = 0; $i < 3; $i++) {
								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => "",
								];
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => "",
							];
						} else if ($check_rows === 1) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];

							for ($i = 0; $i < 2; $i++) {
								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => "",
								];
							}
						} else if ($check_rows === 2) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];

							$sorted[] = [
								'Machine' => $mac['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => "",
							];
						} else if ($check_rows >= 3) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									$ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$cc = $rpt->Count_SeqID($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => $check_rows,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];
						}
					} else {

						if ($check_rows === 0) {
							for ($i = 0; $i < 3; $i++) {
								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => "",
								];
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => "",
							];
						} else if ($check_rows === 1) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									// $ply = 2;
									// $ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$ratemaster = $rpt->RateMaster_PLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									$cc = $rpt->Count_SeqIDPLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									// echo "<pre>".print_r($cc,true)."</pre>";
									// exit();	
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];

							for ($i = 0; $i < 2; $i++) {
								$sorted[] = [
									'Machine' => $mac['Machine'],
									'rowspan' => 3,
									'EmployeeID' => "",
									'Name' => "",
									'BuildType' => "",
									'SCH' => "",
									'Act' => "",
									'P1' => "",
									'P2' => "",
									'P3' => "",
									'Charge' => "",
									'Total' => "",
									'Total_Diff' => "",
									'Qty1' => "",
									'Qty2' => "",
									'Qty3' => "",
								];
							}
						} else if ($check_rows === 2) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									// $ply = 2;
									// $ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$ratemaster = $rpt->RateMaster_PLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									$cc = $rpt->Count_SeqIDPLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									// echo "<pre>".print_r($cc,true)."</pre>";
									// exit();	
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => 3,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];

							$sorted[] = [
								'Machine' => $mac['Machine'],
								'rowspan' => 3,
								'EmployeeID' => "",
								'Name' => "",
								'BuildType' => "",
								'SCH' => "",
								'Act' => "",
								'P1' => "",
								'P2' => "",
								'P3' => "",
								'Charge' => "",
								'Total' => "",
								'Total_Diff' => "",
								'Qty1' => "",
								'Qty2' => "",
								'Qty3' => "",
							];
						} else if ($check_rows >= 3) {
							$total = 0;
							foreach ($report as $re) {
								if ($re["Machine"] === $mac["Machine"]) {
									// $ply = 2;
									// $ratemaster = $rpt->RateMaster($tstart, $tend, $mac["Machine"], $re["BuildTypeId"]);
									$ratemaster = $rpt->RateMaster_PLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									$cc = $rpt->Count_SeqIDPLY($tstart, $tend, $mac["Machine"], $re["BuildTypeId"], $re["PLY"]);
									// echo "<pre>".print_r($cc,true)."</pre>";
									// exit();	
									$total_mac = 0;
									$total_diff = 0;

									$qty1 = 0;
									$price1 = 0;
									$qty2 = 0;
									$price2 = 0;
									$qty3 = 0;
									$price3 = 0;
									$formula = 0;
									$payment = 0;
									$headAct1 = '';
									$headAct2 = '';
									$headAct3 = '';
									$count_r = 0;

									foreach ($ratemaster as $key => $m) {
										$formula = $m["Formula"];
										$payment = $m["Payment"];

										if ($m["SeqID"] == 1) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty1 = $m["QtyMin"];
												$price1 = $m["Price"];
											}
											$headAct1 = 'เส้นที่ 1-' . $m["QtyMin"];

											$total_mac += floor($price1);
										} else if ($m["SeqID"] == 2) {
											if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) >= ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($m["QtyMax"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && ($re["Act"] - $m["QtyMin"]) < ($m["QtyMax"] - $m["QtyMin"]) && $payment != 3) {
												$qty2 = $m["QtyMin"];
												$price2 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											} else if ($re["Act"] >= $m["QtyMin"] && $payment == 3) {
												$qty2 = $m["QtyMin"];
												$price2 = floor(($re["Act"] - ($m["QtyMin"] - 1)) / $formula);
											}
											if ($cc > 2) {
												$headAct2 = 'เส้นที่ ' . $m["QtyMin"] . '-' . $m["QtyMax"];
											} else if ($cc == 2) {
												$headAct2 = 'มากกว่า > ' . ($m["QtyMin"] - 1);
											}

											$total_mac += floor($price2);
										} else if ($m["SeqID"] == 3) {
											if ($re["Act"] >= $m["QtyMin"]) {
												$qty3 = $m["QtyMin"];
												$price3 = ($re["Act"] - ($m["QtyMin"] - 1)) * $m["Price"];
											}

											$headAct3 = 'มากกว่า > ' . ($m["QtyMin"] - 1);

											$total_mac += floor($price3);
										}
									}

									if ($total_mac == 0) {
										$total_diff = 0;
										$total += 0;
									} else {
										$total_diff = floor($total_mac) - $re['Charge'];
										$total += (floor($total_mac) - $re['Charge']);
									}


									$sorted[] = [
										'Machine' => $mac['Machine'],
										'rowspan' => $check_rows,
										'EmployeeID' => $re['EmployeeID'],
										'Name' => $re['Name'],
										'BuildType' => $re['BuildType'],
										'SCH' => $re['SCH'],
										'Act' => $re['Act'],
										'P1' => floor($price1),
										'P2' => 0,
										'P3' => floor($price2),
										'Charge' => $re['Charge'],
										'Total' => floor($total_mac),
										'Total_Diff' => floor($total_diff),
										'Qty1' => $headAct1,
										'Qty2' => "",
										'Qty3' => $headAct2
									];
								}
							}

							$sorted2[] = [
								'Machine' => $mac['Machine'],
								'Sum_Total' => floor($total),
							];
						}
					}
				}
			}
		}
		// echo "<pre>".print_r($sorted2,true)."</pre>";
		// echo "<pre>".print_r($sorted,true)."</pre>";

		// exit();	

		renderView("pagemaster/pdf_ratebuild", [
			"data" => $sorted,
			"data2" => $sorted2,
			"date" => $date_rate,
			"shift" => $shift_th
		]);
	}

	public function genRatecuringPDF()
	{

		$date_rate = filter_input(INPUT_POST, "date_rate");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_rate));

		$export_date = date("d-m-Y H:i:s");

		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		$rpt = new ReportService;

		$getuser = $rpt->getUser($tstart, $tend);
		$report = $rpt->RateCureServicepdf($tstart, $tend);

		$sorted = [];
		foreach ($getuser as $key => $value) {
			// echo $value["CreateBy"]."  **".$rpt->countRowbyUser($tstart, $tend, $value['CreateBy']);
			// echo "<br>";
			$check_rows = $rpt->countRowByUser($tstart, $tend, $value['CreateBy']);
			foreach ($report as $r) {
				if ($r['CreateBy'] === $value['CreateBy']) {
					$check_arm = $rpt->countArmByPress($tstart, $tend, $value['CreateBy'], $r['PressNo']);

					$sorted[] = [
						'CreateBy' => $r['CreateBy'],
						'rowspan' => $check_rows,
						'rowspan_arm' => $check_arm,
						'EmployeeID' => $r['EmployeeID'],
						'Name' => $r['Name'],
						'PressNo' => $r['PressNo'],
						'PressSide' => $r['PressSide'],
						'CuringCode' => $r['CuringCode'],
						'TopTurn' => $r['rate12'],
						'Act' => $r['Act'],
						'RatePay' => $r['RatePay'],
						'TOTAL' => $r['TOTAL']
					];
				}
			}
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		renderView("pagemaster/pdf_ratecuring", [
			"data" => $sorted,
			"date" => $date_rate,
			"shift" => $shift_th,
			"export_date" => $export_date
		]);
	}

	public function genLogbuildingPDF()
	{
		$by = filter_input(INPUT_POST, "by");

		$date = filter_input(INPUT_POST, "date");
		$ss = filter_input(INPUT_POST, "shift");
		$date_con = date('Y-m-d', strtotime($date));
		if ($ss == "day") {
			$shift = "กลางวัน";
			$tstart = $date_con . " 08:00:00";
			$tend = $date_con . " 19:59:59";
		} else {
			$shift = "กลางคืน";
			$subdate = str_replace('-', '/', $date_con);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));
			$tstart = $date_con . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
		}

		$machine = $_POST["selectMac"]["0"];

		// echo "<pre>".print_r($tstart,true)."</pre>";
		// echo "<pre>".print_r($tend,true)."</pre>";
		// echo "<pre>".print_r($machine,true)."</pre>";
		// exit();
		if ($by == "Mac") {
			$machine = $_POST["selectMac"]["0"];
			$rpt = new ReportService;
			$report = $rpt->LogBuildingServicepdf_byMac($tstart, $tend, $machine);
			$sorted = [];
			foreach ($report as $r) {
				$sorted[] = [
					'EmployeeID' => $r['EmployeeID'],
					'Name' => $r['Name'],
					'Machine' => $r['Machine'],
					'BuildType' => $r['BuildType'],
					'LoginDate' => $r['LoginDate'],
					'LogoutDate' => $r['LogoutDate'],
					'Act' => $r['Act']
				];
			}

			// echo "<pre>".print_r($sorted,true)."</pre>";
			// exit();


			renderView("pagemaster/pdf_logbuilding_bymac", [
				"data" => $sorted,
				"date" => $date,
				"shift" => $shift,
				"machine" => $machine
			]);
		} else {
			$user = filter_input(INPUT_POST, "UserId");
			$emp_name = filter_input(INPUT_POST, "emp");
			$rpt = new ReportService;
			$report = $rpt->LogBuildingServicepdf_byUser($tstart, $tend, $user);

			// echo "<pre>".print_r($report ,true)."</pre>";
			// exit();

			$sorted = [];
			foreach ($report as $r) {
				$sorted[] = [
					'EmployeeID' => $r['EmployeeID'],
					'Name' => $r['Name'],
					'Machine' => $r['Machine'],
					'BuildType' => $r['BuildType'],
					'LoginDate' => $r['LoginDate'],
					'LogoutDate' => $r['LogoutDate'],
					'Act' => $r['Act']
				];
			}

			// echo "<pre>".print_r($sorted,true)."</pre>";
			// exit();


			renderView("pagemaster/pdf_logbuilding_byuser", [
				"data" => $sorted,
				"date" => $date,
				"shift" => $shift,
				"emp_name" => $emp_name
			]);
		}
	}

	public function genRatecuringPDF_V2()
	{

		$date_rate = filter_input(INPUT_POST, "date_rate");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date = date('Y-m-d', strtotime($date_rate));
		$export_date = date("d-m-Y H:i:s");

		if ($shift == "day") {
			$tstart = $date . " 08:00:00";
			$tend = $date . " 19:59:59";
			$shift_th = "กลางวัน";
		} else {
			$subdate = str_replace('-', '/', $date);
			$tomorrow = date('Y-m-d', strtotime($subdate . "+1 days"));

			$tstart = $date . " 20:00:00";
			$tend = $tomorrow . " 07:59:59";
			$shift_th = "กลางคืน";
		}

		$rpt = new ReportService;

		$getuser = $rpt->getUser($tstart, $tend);
		$report = $rpt->RateCureServicepdf($tstart, $tend);

		// echo "<pre>".print_r($getuser,true)."</pre>";
		// echo "<pre>".print_r($report,true)."</pre>";
		// exit();

		$sorted = [];
		foreach ($getuser as $key => $value) {
			// echo $value["CreateBy"]."  **".$rpt->countRowbyUser($tstart, $tend, $value['CreateBy']);
			// echo "<br>";
			$check_rows = $rpt->countRowByUser($tstart, $tend, $value['CreateBy']);
			foreach ($report as $r) {
				if ($r['CreateBy'] === $value['CreateBy']) {
					// $check_arm = $rpt->countArmByPress($tstart, $tend, $value['CreateBy'], $r['PressNo']);

					$sorted[] = [
						'CreateBy' => $r['CreateBy'],
						'rowspan' => $check_rows,
						'rowspan_arm' => 2,
						'EmployeeID' => $r['EmployeeID'],
						'Name' => $r['Name'],
						'PressNo' => $r['PressNo'],
						'PressSide' => $r['PressSide'],
						'CuringCode' => $r['CuringCode'],
						'TopTurn' => $r['rate12'],
						'Act' => $r['Act'],
						'RatePay' => $r['RatePay'],
						'TOTAL' => $r['TOTAL']
					];
				}
			}
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		renderView("pagemaster/pdf_ratecuring", [
			"data" => $sorted,
			"date" => $date_rate,
			"shift" => $shift_th,
			"export_date" => $export_date
		]);
	}

	public function ScrapChecking()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("pagemaster/report_scrapchecking");
	}

	public function ScrapCheckingPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		//	print_r($_REQUEST); exit();
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		// echo "<pre>".print_r($date,true)."</pre>";
		// echo "<pre>".print_r($product_group,true)."</pre>";
		// echo "<pre>".print_r($pressBOI,true)."</pre>";
		// exit();

		try {
			$data = $this->report->ScrapChecking($date, $product_group, $pressBOI);

			if (count($data) === 0) {
				echo "No Data!";
				//	var_dump($data);
			} else if (count($data) > 0) {
				// var_dump($data);
				if ($check == 1) {
					renderView("pagemaster/pdf_scarpchecking", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				} else {
					renderView("pagemaster/excel_scarpchecking", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				}
			} else {
				echo "No Data!";
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}



		var_dump($data);
		exit;
	}

	public function loadtirePDF()
	{
		$date = filter_input(INPUT_POST, "dateloadtire");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$date = date('Y-m-d', strtotime($date));

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		if (isset($_POST['selectbrand'])) {
			$brand_select = $_POST['selectbrand'];
			$brand  = '';
			foreach ($brand_select as $v) {
				$brand .= $v . ', ';
			}
			$brand = trim($brand, ', ');
			//convertforin(implode(',',$_POST["selectbrand"]));
		}






		$rows = $this->report->getReportloadtire($date, $product_group, $pressBOI, $brand);


		if ($check == 1) {
			renderView('pagemaster/loadtirePDF', [
				'rows' => $rows,
				'date' => $date,
				'BOI' => $pressBOI



			]);
		}
		if ($check == 2) {
			renderView('pagemaster/loadtireExcel', [
				'rows' => $rows,
				'date' => $date,
				'BOI' => $pressBOI



			]);
		}
	}

	public function warehousrOnhandPDF()
	{
		//$date = filter_input(INPUT_POST, "dateloadtire");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		//$date = date('Y-m-d', strtotime($date));

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		if (isset($_POST['selectbrand'])) {
			$brand_select = $_POST['selectbrand'];
			$brand  = '';
			foreach ($brand_select as $v) {
				$brand .= $v . ', ';
			}
			$brand = trim($brand, ', ');
			//convertforin(implode(',',$_POST["selectbrand"]));
		}

		$rows = $this->report->ReporwarehouseOnhand($product_group, $pressBOI, $brand);


		if ($check == 1) {
			renderView('pagemaster/warehouseonhandPdf', [
				'rows' => $rows,
				'BOI' => $pressBOI
			]);
		}
		if ($check == 2) {
			renderView('pagemaster/warehouseonhandExcel', [
				'rows' => $rows,
				'BOI' => $pressBOI
			]);
		}
	}

	public function greentirecodepdf()
	{
		//$date = filter_input(INPUT_POST, "dateloadtire");
		//$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$GT 	 = 'filter_input(INPUT_POST, "selectMenuGT")';
		//$date = date('Y-m-d', strtotime($date));

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'SM0908';
		} else {
			$product_group = 'SM0907';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		$pressGT  = self::convertforinselect(implode(',', $_POST["selectMenuGT"]));

		if (!isset($_POST['selectMenuGT'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Greentire Code<br/>";
			echo "<a href='/report/grentrecode'>กลับไป</a>";
			exit;
		}


		$rows = $this->report->Reportgreentirecode($product_group, $pressBOI, $pressGT);


		renderView('pagemaster/greentirecode', [
			'rows' => $rows,
			'BOI' => $pressBOI,
			'GT_COD' => $pressGT
		]);
	}

	public function buildingcodepdf()
	{
		$date = filter_input(INPUT_POST, "date_building");
		//$check = filter_input(INPUT_POST, "check_type");
		$shift = filter_input(INPUT_POST, "shift");
		//$BOI 	 = 'filter_input(INPUT_POST, "selectMenuBOI")';
		$GT 	 = 'filter_input(INPUT_POST, "selectMenuGT")';
		$date = date('Y-m-d', strtotime($date));
		//$date = date('Y-m-d', strtotime($date));

		// $item_group = $_POST['item_group'];

		// $product_group = 'TBR';
		// if ($item_group === 'tbr') {
		// 	$product_group = 'SM0908';
		// } else {
		// 	$product_group = 'SM0907';
		// }

		$pressGT  = self::convertforinselect(implode(',', $_POST["selectMenuGT"]));

		if (!isset($_POST['selectMenuGT'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Greentire Code<br/>";
			echo "<a href='/report/grentrecode'>กลับไป</a>";
			exit;
		}


		$rows = $this->report->ReportBuildingcode($date, $shift, $pressGT);


		renderView('pagemaster/buildingcode', [
			'rows' => $rows,
			'GT_COD' => $pressGT,
			'date' => $date,
			'shift' => $shift
		]);
	}

	public function buildingAccPdf()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			// echo "<a href='/report/building_acc'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}

		$arr = $this->report->BuildingAccpdf($datebuilding, $shift, $group, $product_group, $pressBOI);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					'Q5' => '',
					'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		$datagroup = $json_decode[0]->Description;
		renderView("report/pdf_building_acc", [
			"datajson" => $json_decode,
			"date_building" => $date_building,
			"shift" => $shift,
			"group" => $datagroup,
			"BOIName" => $dataBOIName
		]);
	}

	public function greentireScrapAccPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		//	print_r($_REQUEST); exit();
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		try {
			$data = $this->report->greentireScrapAcc($date, $product_group, $pressBOI);

			if (count($data) === 0) {
				echo "No Data!";
				//	var_dump($data);
			} else {
				//var_dump($data);
				if ($check == 1) {
					renderView("page/report_greentire_scrapAcc_pdf", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				} else {
					renderView("page/report_curetire_scrapacc_xcell", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}



		// var_dump($data); exit;


	}

	public function greentireRepairFinalPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		//	print_r($_REQUEST); exit();
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		try {
			$data = $this->report->greentirefinalrepair($date, $product_group, $pressBOI);
			// var_dump($data);
			// exit;
			if (count($data) === 0) {
				echo "No Data!";
			} else {

				if ($check == 1) {
					renderView("page/report_greentire_finalrepair_pdf", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				} else {
					renderView("page/report_greentire_finalrepair_excel", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}



		// var_dump($data); exit;


	}

	public function greentireRepairPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$item_group = filter_input(INPUT_POST, "item_group");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		//	print_r($_REQUEST); exit();
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		try {
			$data = $this->report->greentirerepair($date, $product_group, $pressBOI);

			if (count($data) === 0) {
				echo "No Data!";
			} else {
				// var_dump($data);
				// exit();
				if ($check == 1) {
					renderView("page/report_greentire_repair_pdf", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				} else {
					renderView("page/report_greentire_repair_excel", [
						"data" => $data,
						"date" => $date,
						"BOIName" => $dataBOIName
					]);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}



		// var_dump($data); exit;


	}

	public function finaltowh()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_final_to_wh");
	}

	public function FinalWhPdf()
	{
		$date = filter_input(INPUT_POST, "date_scrap");
		$check = filter_input(INPUT_POST, "check_type");
		$shift = filter_input(INPUT_POST, "shift");
		$time_selected = $_POST["selectTruck"];
		$timeset = [];
		$total = 0;
		if (!isset($_POST['selectTruck'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือก คันรถ<br/>";
			echo "<a href='/report/finaltowh/report'>กลับไป</a>";
			exit;
		}

		foreach ($time_selected as $k => $time_id) {



			$data[] = "'" . $time_id . "'";
		}
		$trucknumber =  implode(",", $data);

		//echo $trucknumber;



		try {
			$data  = (new FinalService)->getReportFinalToWh($date, $shift, $trucknumber);
			$getboiler = (new FinalService)->getroundFinalToWh($date, $shift, $trucknumber);
			// echo "<pre>" . print_r($data, true) . "</pre>";
			// echo "<pre>" . print_r($getboiler, true) . "</pre>";

			$totaltruckround = 0;
			$checktruck = "";
			$checkround = "";
			foreach ($data as $value) {

				if ($checktruck == $value['TruckID'] && $checkround == $value["JournalDescription"]) {
					$totaltruckround += $value['qty'];
					$tempdata[] = [
						'TruckID' => $value['TruckID'],
						'descjour' => $value["JournalDescription"],
						'ItemID' => $value['ItemID'],
						'NameTH' => $value['NameTH'],
						'qty' => $value['qty'],
						'Batch' => $value['Batch'],
						'Craeatedate' => $value['CreateDate'],
						'Totalqty' => $totaltruckround
					];
				} else {
					$totaltruckround = 0;
				}
				$checktruck = $value['TruckID'];
				$checkround = $value["JournalDescription"];
			}
			// echo "<pre>" . print_r($tempdata, true) . "</pre>";
			// exit();
			foreach ($getboiler as $key => $value) {

				// echo $value["TruckID"] . "**" . (new FinalService)->countround($date, $shift, $value['TruckID'], $value['desjornal']);
				// echo "<br>";
				$check_rows = (new FinalService)->countround($date, $shift, $value['TruckID'], $value['desjornal']);

				if ($check_rows === 1) {
					$total1 = 0;
					foreach ($data as $k => $r) {
						if ($r['TruckID'] === $value['TruckID'] && $r['JournalDescription'] === $value['desjornal']) {
							$total1 += $r['qty'];
							$sorted[] = [
								'TruckID' => $r['TruckID'],
								'descjour' => $r["JournalDescription"],
								'rowspan' => 1,
								'ItemID' => $r['ItemID'],
								'NameTH' => $r['NameTH'],
								'ID' => $r['ID'],
								'Batch' => $r['Batch'],
								'Craeatedate' => $r['CreateDate'],
								'qty' => $r['qty'],
								'total' => $total1,
								'k' => $k + 1

							];
						}
					}
				}

				if ($check_rows === 2) {
					$total2 = 0;
					$i = 0;
					foreach ($data as $k => $r) {
						if ($r['TruckID'] === $value['TruckID'] && $r['JournalDescription'] === $value['desjornal']) {
							$total2 += $r['qty'];
							$sorted[] = [
								'TruckID' => $r['TruckID'],
								'descjour' => $r["JournalDescription"],
								'rowspan' => 2,
								'ItemID' => $r['ItemID'],
								'NameTH' => $r['NameTH'],
								'ID' => $r['ID'],
								'Batch' => $r['Batch'],
								'Craeatedate' => $r['CreateDate'],
								'qty' => $r['qty'],
								'total' => $total2,
								'k' => $i
							];
							$i++;
						}
					}
				}

				if ($check_rows > 2) {
					$total3 = 0;
					$i = 0;
					$ii = 0;
					foreach ($data as $k => $r) {
						if ($r['TruckID'] === $value['TruckID'] && $r['JournalDescription'] === $value['desjornal']) {
							$total3 += $r['qty'];
							// if($i <= $check_rows){
							if (($i + 1) < $check_rows) {
								$ii = 0;
							} else {
								$ii = $i;
							}
							$sorted[] = [
								'TruckID' => $r['TruckID'],
								'descjour' => $r["JournalDescription"],
								'rowspan' => $check_rows,
								'ItemID' => $r['ItemID'],
								'NameTH' => $r['NameTH'],
								'ID' => $r['ID'],
								'Batch' => $r['Batch'],
								'Craeatedate' => $r['CreateDate'],
								'qty' => $r['qty'],
								'total' => $total3,
								'k' => $ii
							];
							$i++;
						}
					}
				}
			}

			// echo "<pre>" . print_r($date, true) . "</pre>";


			// exit();

			if (count($data) === 0) {
				echo "No Data!";
				// var_dump($data);
			} else {
				// var_dump($data);
				// exit();
				if ($check == 1) {
					renderView("page/final_to_warehouse_report_pdf", [
						"data" => $sorted,
						"date" => $date,
						"shift" => $shift
					]);
				} else {
					renderView("page/final_to_warehouse_report_excel", [
						"data" => $sorted,
						"date" => $date,
						"shift" => $shift
					]);
				}
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	public function MovementIssueExcel($rowdata, $check)
	 {
		 
		$data = (new ReportService)->MovementIssue($rowdata);
		//  print_r($data);
		//  exit;
		$dataloading = json_decode($data);
		//   print_r($dataloading);
		//  exit;
	// 	$dataloadingexport = json_decode($dataexport);

		renderView('report/Movementissue_excel', [
		"rowdata" => $dataloading
		]);
	}

	//quality report
	public function qualityexcel()
	{
	  $date = filter_input(INPUT_POST, "param_date");
	  $shift = filter_input(INPUT_POST, "shift");
	  $datequality = date('Y-m-d', strtotime($date));
	  $datequality_nextday = date('Y-m-d', strtotime($date . ' +1 days'));

	  if ($shift == 'day') {
		$timeset = 'QC.CreateDate >= \'' . $datequality . ' 08:00:00\' AND QC.CreateDate <= ' . '\'' . $datequality . ' 19:59:59\'';
	} else {
		$timeset = 'QC.CreateDate >= \'' . $datequality . ' 20:00:00\' AND QC.CreateDate <= ' . '\'' . $datequality_nextday . ' 07:59:59\'';
	}
	
	$rows = (new ReportService)->qualityreport($timeset);

	renderView('page/report_quality_checking', [
			'rows' => $rows,
			'timeset' => $timeset,
			'shift' => $shift,
			'date' => $datequality
			
		  ]);
	}

	// Final INS Report
	public function finalreportins()
	{
		$date = filter_input(INPUT_POST, "param_date");
		$shift = filter_input(INPUT_POST, "param_shift");
		$type = filter_input(INPUT_POST, "param_type");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI    = 'filter_input(INPUT_POST, "selectMenuBOI")';
		
		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
				$strploblem .= $value;
				} else {
				$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		$pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);

		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		
		//$result = $this->report->finalreportins($date, $shift, $type, $pressBOI);
		$result = (new ReportService)->finalreportins($date, $shift, $type, $pressBOI);

		// print_r($result);
		// exit();
		
		if ($result !== null) {
			if ($check == 1) {
				renderView('page/final_ins_topdf', [
				'data' => $result,
				'date' => $date,
				'shift' => (int) $shift === 1 ? 'กลางวัน' : 'กลางคืน',
				'type' => strtoupper($type),
				'BOIName' => $dataBOIName,
				]);
			} else {
				renderView('page/final_ins_toexcel', [
				'data' => $result,
				'date' => $date,
				'shift' => (int) $shift === 1 ? 'กลางวัน' : 'กลางคืน',
				'type' => strtoupper($type),
				'BOIName' => $dataBOIName,
				]);
			}
		} else {
			echo 'data not found!';
		}
	}
	
	public function repairinventory()
	{
		$date = filter_input(INPUT_POST, "param_date");
		$product_group = filter_input(INPUT_POST, "param_type");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI    = 'filter_input(INPUT_POST, "selectMenuBOI")';
		
		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
				$strploblem .= $value;
				} else {
				$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		if ($product_group === 'pcr') {
			$product_group = 'RDT';
		} else {
			$product_group = 'TBR';
		}
		
		$pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);

		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}
		
		$result = (new ReportService)->repairinventory($product_group, $pressBOI);

		// print_r($result);
		// exit();
		// var_dump($result);
		// exit();
		// echo "<pre>";
		// // print_r(json_decode($result));
		// print_r($result);
		// echo "</pre>";
		// exit();
		
		if ($result !== null) {
			if ($check == 1) {
				renderView('page/repairinventory_topdf', [
				'data' => $result,
				'BOIName' => $dataBOIName,
				'product_group' => $product_group
				]);
			} else {
				renderView('page/repairinventory_toexcel', [
				'data' => $result,
				'BOIName' => $dataBOIName,
				'product_group' => $product_group
				]);
			}
		} else {
			echo 'data not found!';
		}
	}

	// Daily Repair Report
	public function dailyrepair()
	{
		$date = filter_input(INPUT_POST, "param_date");
		$shift = filter_input(INPUT_POST, "param_shift");
		$type = filter_input(INPUT_POST, "param_type");
		$check = filter_input(INPUT_POST, "check_type");
		$BOI    = 'filter_input(INPUT_POST, "selectMenuBOI")';
		
		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
				$strploblem .= $value;
				} else {
				$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}

		$pressBOI  = convertforin(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);

		if ($dataBOIName == "") {
			$dataBOIName = "ALL";
		}

		//  echo $dataBOIName;
		//  exit();
		//$result = $this->report->dailyrepair($date, $shift, $type, $pressBOI);
		$result = (new ReportService)->dailyrepair($date, $shift, $type, $pressBOI);

		// var_dump($result);
		// exit();
		
		if ($result !== null) {
			if ($check == 1) {
				renderView('page/dailyrepair_topdf', [
				'data' => $result,
				'date' => $date,
				'shift' => (int) $shift === 1 ? 'กลางวัน 08:00 - 19:59:59' : '>> กลางคืน 20:00 - 07:59:59',
				'type' => strtoupper($type),
				'BOIName' => $dataBOIName,
				]);
			} 
			else {
				renderView('page/dailyrepair_toexcel', [
				'data' => $result,
				'date' => $date,
				'shift' => (int) $shift === 1 ? 'กลางวัน' : 'กลางคืน',
				'type' => strtoupper($type),
				'BOIName' => $dataBOIName,
				]);
			}
		} else {
			echo 'data not found!';
		}
	}

	public function genlightbuffinventoryPDF()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/lightbuff/inventory'>กลับไป</a>";
			exit;
		}

		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		$item_group = $_POST['item_group'];
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$check = filter_input(INPUT_POST, "check_type");

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$pressBOI  = $this->convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}

		$arr = $this->report->BuffInventoryServiceallpdf($product_group, $pressBOI);
		$json_decode  = json_decode($arr);
		// echo "<pre>".print_r($json_decode,true)."</pre>";
		//exit();
		if ($check == 1) {
			renderView("pagemaster/pdf_lightbuffinventory", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		} else {

			renderView("pagemaster/pdf_lightbuffinventoryexcel", [
				"datajson" => $json_decode,
				"date" => $date,
				"time" => $time,
				"BOIName" => $dataBOIName
			]);
		}
	}

	public function BuffreportServiceallpdf()
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$datelight = date('Y-m-d', strtotime($date));
		// $datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$BOI 	 = filter_input(INPUT_POST, "selectMenuBOI");
		$check = filter_input(INPUT_POST, "check_type");
		$item_group = $_POST['item_group'];
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'PCR';
		}
		$pressBOI  = self::convertforinselect(implode(',', $_POST["selectMenuBOI"]));
		$dataBOIName = $this->boi->BOIName($pressBOI);
		if ($dataBOIName == "" || $dataBOIName == 1) {
			$dataBOIName = "ALL";
		}
		$arr = $this->report->BuffreportServiceallpdf($datelight,$shift, $product_group, $pressBOI);
		$json_decode  = json_decode($arr);
		// echo "<pre>".print_r($json_decode,true)."</pre>";
		// exit();
		if ($check == 1) {
			renderView('pagemaster/pdf_lightreport', [
				'datajson' => $json_decode,
				'shift' => $shift,
				'date' => $datelight,
				'BOIName' => $dataBOIName,
				'Type' => $product_group
			]);
		}
		if ($check == 2) {
			renderView("pagemaster/excel_lightreport", [
				'datajson' => $json_decode,
				'shift' => $shift,
				'date' => $datelight,
				'BOIName' => $dataBOIName,
				'Type' => $product_group
			]);
		}
	}

	public function FinalHoldExcel($producGroup){
		$data = (new OnhandService)->getFinalHold($producGroup);
		$json_decode  = json_decode($data);
		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		renderView("pagemaster/pdf_finalholdexcel", [
			"datajson" => $json_decode,
			"date" => $date,
			"time" => $time,
			"BOIName" => $producGroup
		]);
	}
}