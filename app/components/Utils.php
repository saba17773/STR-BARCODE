<?php

namespace App\Components;

use App\Components\Security;
use App\Components\Utils;
use App\V2\Batch\BatchAPI;

class Utils
{
	public static function genTransId($barcode_decode)
	{
		return $barcode_decode . substr(date('YmdHis'), 2) . microtime(true) * 10000;
		// return $barcode_decode . substr(date('YmdHis'), 2).uniqid();
	}

	public static function arr2str($arr)
	{
		$str = "";

		if (count($arr) === 0 || $arr === null || $arr === "") {
			return $str;
		}

		try {
			foreach ($arr as $value) {
				$str .= $value . ",";
			}

			return trim($str, ",");
		} catch (\Exception $e) {
			return "";
		}
	}

	public static function isMobile()
	{
		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			echo json_encode(["status" => 200]);
		} else {
			echo json_encode(["status" => 400]);
		}
	}

	public static function isMobileDevice()
	{
		$detect = new \Mobile_Detect;
		if ($detect->isMobile()) {
			return true;
		} else {
			return false;
		}
	}

	public static function getWeek($datetime)
	{
		return (new BatchAPI)->getBatch($datetime, '');
	}

	public static function getWeekNormal($datetime, $isBuild = false, $check = '')
	{
		return (new BatchAPI)->getBatch($datetime, '', $isBuild, $check);
		// $date =  date('Y-m-d H:i:s', strtotime($datetime));
		// $ddate = new \DateTime($date);
		// $year = $ddate->format('Y');
		// $week = $ddate->format("W");

		// return $year . '-' . $week;
	}
}
