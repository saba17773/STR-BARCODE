<?php

namespace App\Controllers;

use App\Services\CureTireService;

class CureTireController
{
	private $cureTireApi = null;

	public function __construct()
	{
		$this->cureTireApi = new CureTireService();
	}

	public function all()
	{
		echo $this->cureTireApi->all();
	}

	public function create()
	{
		try {
			// code
			$id_name = filter_input(INPUT_POST, "id_name");
			$des_name = filter_input(INPUT_POST, "des_name");
			$item_name = filter_input(INPUT_POST, "item_name");
			$gt_name = filter_input(INPUT_POST, "gt_name");
			$form_type = filter_input(INPUT_POST, "form_type");

			if ($form_type == "create") {
				$create = $this->cureTireApi->create($id_name, $des_name, $item_name, $gt_name);
				return json_encode($create);
			}

			if ($form_type == "update") {
				$update = $this->cureTireApi->update($des_name, $item_name, $gt_name, $id_name);
				return json_encode($update);
			}
		} catch (\Exception $e) {
			return json_encode(["result" => false, "message" => $e->getMessage()]);
		}
	}

	public function batchchang()
	{
		echo $this->cureTireApi->changbath();
	}

	public function updatebatch()
	{
		try {
			// code
			$_id = filter_input(INPUT_POST, "_id");
			$Date = filter_input(INPUT_POST, "Date");
			$timeset = filter_input(INPUT_POST, "timeset");
			$datcal = self::checkcalweek($Date, $timeset);
			$caldatetime = (explode(",", $datcal));
			$caldate = $caldatetime[0];
			$caltime = $caldatetime[1];

			// return json_encode(["result" => false, "message" => $caltime]);

			// exit();



			$update = $this->cureTireApi->updatebatch($_id, $Date, $timeset, $caldate, $caltime);
			return json_encode($update);
		} catch (\Exception $e) {
			return json_encode(["result" => false, "message" => $e->getMessage()]);
		}
	}

	public function checkcalweek($Date, $timeset)
	{


		if ($Date == 1) {
			$caldate = '0';

			if ($timeset == '00:00:00') {
				$calhours = '+24';
			} else if ($timeset == '01:00:00') {
				$calhours = '+23';
			} else if ($timeset == '02:00:00') {
				$calhours = '+22';
			} else if ($timeset == '03:00:00') {
				$calhours = '+21';
			} else if ($timeset == '04:00:00') {
				$calhours = '+20';
			} else if ($timeset == '05:00:00') {
				$calhours = '+19';
			} else if ($timeset == '06:00:00') {
				$calhours = '+18';
			} else if ($timeset == '07:00:00') {
				$calhours = '+17';
			} else if ($timeset == '08:00:00') {
				$calhours = '+16';
			} else if ($timeset == '09:00:00') {
				$calhours = '+15';
			} else if ($timeset == '10:00:00') {
				$calhours = '+14';
			} else if ($timeset == '11:00:00') {
				$calhours = '+13';
			} else if ($timeset == '12:00:00') {
				$calhours = '+12';
			} else if ($timeset == '13:00:00') {
				$calhours = '+11';
			} else if ($timeset == '14:00:00') {
				$calhours = '+10';
			} else if ($timeset == '15:00:00') {
				$calhours = '+9';
			} else if ($timeset == '16:00:00') {
				$calhours = '+8';
			} else if ($timeset == '17:00:00') {
				$calhours = '+7';
			} else if ($timeset == '18:00:00') {
				$calhours = '+6';
			} else if ($timeset == '19:00:00') {
				$calhours = '+5';
			} else if ($timeset == '20:00:00') {
				$calhours = '+4';
			} else if ($timeset == '21:00:00') {
				$calhours = '+3';
			} else if ($timeset == '22:00:00') {
				$calhours = '+2';
			} else if ($timeset == '23:00:00') {
				$calhours = '+1';
			}
		} else {
			if ($Date == 1) {
				$caldate = '0';
			} else if ($Date == 2) {
				$caldate = '0';
			} else if ($Date == 3) {
				$caldate = '-1';
			} else if ($Date == 4) {
				$caldate = '-2';
			} else if ($Date == 5) {
				$caldate = '-3';
			} else if ($Date == 6) {
				$caldate = '-4';
			} else if ($Date == 7) {
				$caldate = '+1';
			}
			// house calculate
			if ($timeset == '00:00:00') {
				$calhours = '-24';
			} else if ($timeset == '01:00:00') {
				$calhours = '-25';
			} else if ($timeset == '02:00:00') {
				$calhours = '-26';
			} else if ($timeset == '03:00:00') {
				$calhours = '-27';
			} else if ($timeset == '04:00:00') {
				$calhours = '-28';
			} else if ($timeset == '05:00:00') {
				$calhours = '-29';
			} else if ($timeset == '06:00:00') {
				$calhours = '-6';
			} else if ($timeset == '08:00:00') {
				$calhours = '-8';
			} else if ($timeset == '09:00:00') {
				$calhours = '-9';
			} else if ($timeset == '10:00:00') {
				$calhours = '-10';
			} else if ($timeset == '11:00:00') {
				$calhours = '-11';
			} else if ($timeset == '12:00:00') {
				$calhours = '-12';
			} else if ($timeset == '13:00:00') {
				$calhours = '-13';
			} else if ($timeset == '14:00:00') {
				$calhours = '-14';
			} else if ($timeset == '15:00:00') {
				$calhours = '-15';
			} else if ($timeset == '16:00:00') {
				$calhours = '-16';
			} else if ($timeset == '17:00:00') {
				$calhours = '-17';
			} else if ($timeset == '18:00:00') {
				$calhours = '-18';
			} else if ($timeset == '19:00:00') {
				$calhours = '-19';
			} else if ($timeset == '20:00:00') {
				$calhours = '-20';
			} else if ($timeset == '21:00:00') {
				$calhours = '-21';
			} else if ($timeset == '22:00:00') {
				$calhours = '-22';
			} else if ($timeset == '23:00:00') {
				$calhours = '-23';
			}
		}


		$datcal = $caldate . "," . $calhours;

		return $datcal;
	}
}
