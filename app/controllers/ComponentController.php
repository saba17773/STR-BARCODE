<?php

namespace App\Controllers;

use App\Conpnents\Database as DB;
use Wattanar\Sqlsrv;
use App\Models\Barcode;
use App\Services\ComponentService;
use App\Services\DefectService;
use App\Components\Utils;
use App\Components\Security;
use App\Components\Authentication;

class ComponentController
{
	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
	}

	public function component()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component");
	}

	public function component_TMC()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_2");
	}

	public function component_BEI()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_3");
	}

	public function component_BST()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_4");
	}

	public function component_STF()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_5");
	}

	public function component_SHW()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_6");
	}

	public function component_SWL()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_7");
	}

	public function component_TRD()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_8");
	}

	public function component_BEL()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_9");
	}

	public function component_INL()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_10");
	}

	public function component_NCH()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_11");
	}

	public function component_WCH()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_12");
	}

	public function component_PLY()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_13");
	}

	public function component_barcode()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_barcode");
	}

	public function component_item()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_item");
	}

	public function component_report()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_report");
	}

	public function component_gen_item()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView("pagecomponent/component_gen_item");
	}

	public function component_defect()
	{
		$item = filter_input(INPUT_GET, "item");
		echo (new ComponentService)->loaddefect($item);
	}

	public function component_defectcheck()
	{
		$defect = filter_input(INPUT_GET, "defect");
		echo (new ComponentService)->defect($defect);
	}

	public function component_pastcode()
	{
		$item = filter_input(INPUT_GET, "item");
		echo (new ComponentService)->pastcode($item);
	}

	public function component_unit()
	{
		echo (new ComponentService)->loadunit();
	}

	public function component_section()
	{
		echo (new ComponentService)->loadsection();
	}

	public function component_load()
	{
		$date_component = filter_input(INPUT_GET, "date_component");
		$shift = filter_input(INPUT_GET, "shift");

		$date_component = date('Y-m-d', strtotime($date_component));
		$date_component_plus = date('Y-m-d', strtotime($date_component . ' +1 days'));
		echo (new ComponentService)->load($date_component, $date_component_plus, $shift);
	}

	public function insert_barcode()
	{
		$item 			= filter_input(INPUT_POST, "item");
		$pastcode 		= filter_input(INPUT_POST, "pastcode");
		$qty 			= filter_input(INPUT_POST, "qty");
		$shift 			= filter_input(INPUT_POST, "shift");
		$date_component = filter_input(INPUT_POST, "date_component");
		$df = date('Y-m-d', strtotime($date_component));
		// $date_component_plus= date('Y-m-d', strtotime($date_component. ' +1 days'));
		$date_return 		= date('d-m-Y', strtotime($date_component));

		$datetime = date("Y-m-d");
		$batch = Utils::getWeekNormal($datetime);

		if ((new ComponentService)->pastcodecheck($item) === false) {
			echo json_encode(["status" => 404, "message" => "code_null"]);
			exit();
		}

		if ((new ComponentService)->check_status_number($df, $shift) === true) {
			if ($shift == 1) {
				$shiftname = "กลางวัน";
			} else {
				$shiftname = "กลางคืน";
			}
			echo json_encode(["status" => 404, "message" => "status_completed", "shiftname" => $shiftname]);
			exit();
		}

		if ((new ComponentService)->checkqty($df, $shift, $item, $qty) === false) {
			echo json_encode(["status" => 404, "message" => "qty_minus", "pastcode" => $pastcode]);
			exit();
		}

		if ((new ComponentService)->insert_barcode($item, $qty, $batch, $df, $shift) === true) {
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ", "pastcode" => $pastcode, "date_return" => $date_return]);
		} else {
			echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ", "pastcode" => $pastcode]);
		}
	}

	public function update_error()
	{
		$id = filter_input(INPUT_POST, "id");
		// echo $id;
		$update 	= (new ComponentService)->update_error($id);
	}

	public function update_barcode()
	{
		$good = filter_input(INPUT_POST, "good");
		$error = filter_input(INPUT_POST, "error");
		$item = filter_input(INPUT_POST, "item");
		$id = filter_input(INPUT_POST, "id");

		$datetime = date("Y-m-d");
		$batch = Utils::getWeekNormal($datetime);

		// if ($error == 0) {
		// 	echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ Scrap เป็น 0"]);
		// 	exit();
		// }

		$check_defect 	= (new ComponentService)->check_datacomponent($id);
		$defect 		= $check_defect[0]['DefectID'];

		// if ($error == 0 && $defect == null) {
		// 	echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ Defect เป็นค่าว่าง"]);
		// 	exit();
		// }
		$update = (new ComponentService)->update_barcode($good, $error, $item, $id);
		// if ((new ComponentService)->update_barcode($good,$error,$id) === true) {
		// 	echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		// }else{
		// 	echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
		// }
	}

	public function update_defect()
	{
		$defectid = filter_input(INPUT_POST, "defectid");
		$id = filter_input(INPUT_POST, "id");

		if ((new ComponentService)->update_defect($defectid, $id) === true) {
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
		}
	}

	public function update_time()
	{
		$starttime = filter_input(INPUT_POST, "starttime");
		$endtime = filter_input(INPUT_POST, "endtime");
		$id = filter_input(INPUT_POST, "id");

		$data = (new ComponentService)->loaddate($id);

		if (isset($data[0]['StartTime'])) {
			$startdate 	= $data[0]['StartTime'];
			$enddate 	= $data[0]['EndTime'];
			$startdate = substr($startdate, 0, -13);
			$enddate = substr($enddate, 0, -13);
		} else {
			$startdate 	= date('Y-m-d');
			$enddate 	= date('Y-m-d');
		}

		$st = $startdate . ' ' . $starttime;
		$et = $enddate . ' ' . $endtime;

		if ((new ComponentService)->update_time($st, $et, $id) === true) {
			echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
		} else {
		}
	}

	public function report_pdf()
	{
		$date = filter_input(INPUT_POST, "date_component");
		$mode = filter_input(INPUT_POST, "mode");
		$report_type = filter_input(INPUT_POST, "report_type");
		$df 	= date('Y-m-d', strtotime($date));
		// $date_component_ 	= date('Y-m-d', strtotime($date. ' +1 days'));

		if ($mode == 'a') {
			$shift 	= 1;
			// $shift_ = "20:00";
			// $df 	= $date_component." 08:01";
			// $df_ 	= $date_component." 20:00";
		} else {
			$shift 	= 2;
			// $shift_ = "08:00";
			// $df 	= $date_component." 20:01";
			// $df_ 	= $date_component_." 08:00";
		}


		if ($report_type == 1) {
			$cmc			= "CMC";
			$bbr			= "BBR";
			$datacmc 		= (new ComponentService)->Get_reportCMC($df, $report_type, $shift);
			$databbr		= (new ComponentService)->Get_reportBBR($df, $report_type, $shift);
			$json_decodecmc = json_decode($datacmc);
			$json_decodebbr = json_decode($databbr);
			$numbercmc 		= count(array_filter($json_decodecmc));
			$numberbbr 		= count(array_filter($json_decodebbr));
			$numberallcmc 	= (15 - $numbercmc);
			$numberallbbr	= (15 - $numberbbr);

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
			];

			for ($i = 0; $i < $numberallcmc; $i++) {
				foreach ($fake_data[$i] as $value) {
					$sortedcmc = [];
					$json_decodecmc[] = (object) [
						'SCH' => '',
						'GoodQty' => '',
						'ErrorQty' => '',
						'PastCodeID' => '',
						'GroupName' => '',
					];
					$sortedcmc = $json_decodecmc;
				}
			}

			for ($i = 0; $i < $numberallbbr; $i++) {
				foreach ($fake_data[$i] as $value) {
					$sortedbbr = [];
					$json_decodebbr[] = (object) [
						'SCH' => '',
						'GoodQty' => '',
						'ErrorQty' => '',
						'PastCodeID' => '',
						'GroupName' => '',
					];
					$sortedbbr = $json_decodebbr;
				}
			}
		} else {

			$data 			= (new ComponentService)->Get_report($df, $report_type, $shift);
			$json_decode  	= json_decode($data);
			$number 		= count(array_filter($json_decode));

			if ($number < 19) {
				$numberall 	= (19 - $number);
			} else if ($number < 42) {
				$numberall  = (42 - $number);
			} else if ($number < 65) {
				$numberall  = (65 - $number);
			} else if ($number < 88) {
				$numberall  = (88 - $number);
			} else if ($number < 111) {
				$numberall  = (111 - $number);
			} else if ($number < 134) {
				$numberall  = (134 - $number);
			}

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
			];

			$sectionname = $json_decode[0]->SectionName;

			for ($i = 0; $i < $numberall; $i++) {
				foreach ($fake_data[$i] as $value) {
					$sorted = [];
					$json_decode[] = (object) [
						'SCH' => '',
						'GoodQty' => '',
						'ErrorQty' => '',
						'PastCodeID' => '',
						'GroupName' => '',
					];
					$sorted = $json_decode;
				}
			}
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit;

		if ($report_type == 1) {

			renderView("pagecomponent/pdf_" . $report_type, [
				"datacmc" => $sortedcmc,
				"databbr" => $sortedbbr,
				"shiftA" => "",
				"shiftB" => "",
				"mode" => $mode,
				"date" => $date
			]);
		} else {

			renderView("pagecomponent/pdf_" . $report_type, [
				"data" => $sorted,
				"shiftA" => "",
				"shiftB" => "",
				"mode" => $mode,
				"date" => $date,
				"sectionname" => $sectionname
			]);
		}
	}

	public function report_origin_pdf()
	{
		$date 		= filter_input(INPUT_POST, "date_component");
		$mode 		= filter_input(INPUT_POST, "mode");
		$section 	= filter_input(INPUT_POST, "section");
		$df 		= date('Y-m-d', strtotime($date));

		if ($mode == 'a') {
			$shift 	= 1;
		} else {
			$shift 	= 2;
		}

		$data 			= (new ComponentService)->Get_origin_report($df, $section, $shift);
		$json_decode  	= json_decode($data);

		renderView("pagecomponent/pdf_origin", [
			"data" => $json_decode,
			"shiftA" => "",
			"shiftB" => "",
			"mode" => $mode,
			"date" => $date
		]);
	}

	public function update_shift()
	{
		$id 		= filter_input(INPUT_POST, "id");
		$shift  	= filter_input(INPUT_POST, "shift");

		if ($shift == 1) {
			$shift_change = 2;
		} else {
			$shift_change = 1;
		}

		if ((new ComponentService)->update_shift($shift_change, $id) === true) {
			echo json_encode(["status" => 200, "message" => "ย้ายสำเร็จ"]);
		} else {
			echo json_encode(["status" => 404, "message" => "ย้ายไม่สำเร็จ"]);
		}
	}

	public function loadpastcode()
	{
		$section = filter_input(INPUT_GET, "section");
		echo (new ComponentService)->loadpastcode($section);
	}

	public function printpastcode()
	{
		$item = filter_input(INPUT_GET, "item");

		$data = (new ComponentService)->loaditem($item);
		$json_decode = json_decode($data);

		renderView("pagecomponent/pdf_pastcode", ["data" => $json_decode]);
	}

	public function create_item()
	{
		$pastcode 		= filter_input(INPUT_POST, "pastcode");
		$item  			= filter_input(INPUT_POST, "item");
		$item_name  	= filter_input(INPUT_POST, "item_name");
		$section_id  	= filter_input(INPUT_POST, "section_id");
		$form_type 		= filter_input(INPUT_POST, "form_type");
		$id 			= filter_input(INPUT_POST, "id");
		// if ($shift==1) {
		// 	$shift_change = 2;
		// }else{
		// 	$shift_change = 1;
		// }
		if ($form_type == 'create') {
			if ((new ComponentService)->create_item($pastcode, $item, $item_name, $section_id) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		} else {
			if ((new ComponentService)->update_item($pastcode, $item, $item_name, $section_id, $id) === true) {
				echo json_encode(["status" => 200, "message" => "บันทึกสำเร็จ"]);
			} else {
				echo json_encode(["status" => 404, "message" => "บันทึกไม่สำเร็จ"]);
			}
		}
	}

	public function update_status()
	{
		$date_component = filter_input(INPUT_POST, "date_component");
		$date_component = date('Y-m-d', strtotime($date_component));
		$shift 			= filter_input(INPUT_POST, "shift");

		$section_list = implode(',', $_POST['section_list']);
		$section_str = "";
		$a = explode(',', $section_list);
		foreach ($a as $value) {
			if ($section_str === "") {
				$section_str .= $value;
			} else {
				$section_str .= "," . $value;
			}
		}

		$update = (new ComponentService)->update_status($date_component, $shift, $section_str);
	}

	public function check_status()
	{
		$date_component = filter_input(INPUT_GET, "date_component");
		$date_component = date('Y-m-d', strtotime($date_component));
		$shift 			= filter_input(INPUT_GET, "shift");

		// echo (new ComponentService)->check_status($date_component,$shift);
		if ((new ComponentService)->check_status($date_component, $shift) === false) {
			echo json_encode(["status" => 200, "message" => "Transection is Open"]);
		} else {
			echo json_encode(["status" => 404, "message" => "Transection is Completed"]);
		}
	}

	public function check_status_number()
	{
		$date_component = filter_input(INPUT_GET, "date_component");
		$date_component = date('Y-m-d', strtotime($date_component));
		$shift 			= filter_input(INPUT_GET, "shift");
		if ($shift == 1) {
			$shiftname = "กลางวัน";
		} else {
			$shiftname = "กลางคืน";
		}
		if ((new ComponentService)->check_status_number($date_component, $shift) === true) {
			echo json_encode(["status" => 200, "message" => "Transection is Completed", "shiftname" => $shiftname]);
		} else {
			echo json_encode(["status" => 404, "message" => "Transection is Open"]);
		}
	}

	public function getSectionAll()
	{
		echo (new ComponentService)->getSectionData();
	}
}
