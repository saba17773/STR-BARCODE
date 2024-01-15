<?php

namespace App\Controllers;

use App\Services\LoadingService;
use App\Services\BarcodeService;
use App\Services\InventService;
use App\Services\UserService;
use App\Components\Security;
use App\V2\Pallet\PalletAPI;
use App\V2\Barcode\BarcodeAPI;

class LoadingController
{

	public function __construct()
	{
		$this->user = new UserService;
	}

	public function getLoadingTableAllStatus()
	{
		echo (new LoadingService)->getLoadingTableAllStatus();
	}

	public function getLoadingTable($pickingListId)
	{
		echo (new LoadingService)->getLoadingTable($pickingListId);
	}

	public function getLoadingTableAll()
	{
		echo (new LoadingService)->getLoadingTableAll();
	}

	public function getLoadingLine($pickingListId)
	{
		header('Content-Type: application/json');
		echo (new LoadingService)->getLoadingLine($pickingListId);
	}

	public function createLoadingTable($pickingListId)
	{
		if ((new LoadingService)->isPickingListIdExistsInPickingListJour($pickingListId) === false) {
			exit(json_encode([
				'status' => 400,
				'message' => 'ไม่พบ Picking List ID ใน AX'
			]));
		}

		if ((new LoadingService)->isPickingListIdExistsInLoadingTable($pickingListId) === true) {
			exit(json_encode([
				"status" => 200,
				"message" => "Picking List ID มีอยู่แล้วใน Loading Table",
				"pid" => $pickingListId
			]));
		} else {
			$result = (new LoadingService)->createLoadingTable($pickingListId);
		}

		if ($result == 200) {
			exit(json_encode(["status" => 200, "message" => "Successful!", "pid" => $pickingListId]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
		}
	}

	public function savePick()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		$pid = filter_input(INPUT_POST, 'pid');
		$inventTransId = filter_input(INPUT_POST, 'inventTransId');

		// preg_match('/LPN(?:..........)/i', $barcode, $matched);

		// if ( count( $matched ) !== 0 ) {

		// 	if ((new PalletAPI)->isRealLPN($barcode) === true) {
		// 		exit(json_encode([
		// 			"status" => 404,
		// 			"message" => 'LPN ไม่ถูกต้อง หรือปิดไปแล้ว'
		// 		]));
		// 	}

		// 	if ((new PalletAPI)->isComplete($barcode) === false) {
		// 		exit(json_encode([
		// 			"status" => 404,
		// 			"message" => 'LPN ไม่ถูกต้อง หรือปิดไปแล้ว'
		// 		]));
		// 	}

		// 	if ((new PalletAPI)->isQtyInUseMatchLPNLine($barcode) === false) {
		// 		exit(json_encode([
		// 			"status" => 404,
		// 			"message" => 'QTY ไม่ถูกต้อง'
		// 		]));
		// 	}

		// 	$allBarcode = (new PalletAPI)->getBarcodeFromLPNLine($barcode);

		// 	if (count($allBarcode) !== 0) {

		// 		foreach ($allBarcode as $v) {
		// 			$result = (new LoadingService)->savePick($pid, $inventTransId, $v['Barcode']);
		// 		}

		// 		$locationByLPN = (new PalletAPI)->getLocationLPNMaster($barcode);

		// 		(new PalletAPI)->updateQtyInuseLocation($locationByLPN[0]['LocationID']);

		// 		exit(json_encode(["status" => 200, "message" => "Update Successful!"]));

		// 	} else {

		// 		exit(json_encode(["status" => 404, "message" => "Barcode not found in LPN Line"]));
		// 	}

		// 	exit;
		// }

		if ((new LoadingService)->isDenyLoading($barcode) === true) {
			exit(json_encode(["status" => 404, "message" => "Barcode นี้ห้ามโหลด"]));
		}

		if ((new BarcodeService)->isBarcodeFoilNull($barcode) === true) {
			$barcode_type = 'BarcodeFoil';
		} else {
			$barcode_type = 'Barcode';
		}

		if ($barcode_type === 'BarcodeFoil') {
			$barcode = (new BarcodeService)->getBarcodeFromBarcodeFoil($barcode);
		}

		$errText = 'Barcode มีปัญหาห้ามโหลด';

		if ((new BarcodeService)->isRanged($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
			exit(json_encode(["status" => 404, "message" => $errText]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
			exit(json_encode(["status" => 404, "message" => $errText]));
		}

		if ((new InventService)->isPicked($barcode) === true) {
			// exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ยังไม่ Received."]));
			exit(json_encode(["status" => 405, "message" => 'ยิงโหลดซ้ำ']));
		}

		if ((new InventService)->isReceived($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "สถานะ Barcode ยังไม่ Received."]));
			exit(json_encode(["status" => 404, "message" => $errText]));
		}

		if ((new InventService)->checkWarehouseReceiveData($barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ Recived โดย warehouse."]));
			exit(json_encode(["status" => 404, "message" => $errText]));
		}

		if ((new LoadingService)->isItemMatch($pid, $inventTransId, $barcode) === false) {
			// exit(json_encode(["status" => 404, "message" => "ไม่พบ Item number ใน Picking List."]));
			exit(json_encode(["status" => 404, "message" => $errText]));
		}

		$result = (new LoadingService)->savePick($pid, $inventTransId, $barcode);

		if ($result === 901) { // remainder = 0
			exit(json_encode(["status" => 901, "message" => "Remainder = 0"]));
		} else if ($result == 200) {
			// exit(json_encode(["status" => 200, "message" => "Update Successful!"]));
			exit(json_encode([
				"status" => 200,
				"message" => $barcode,
				"curecode" => (new BarcodeAPI)->getCureCode($barcode),
				'batch' => (new BarcodeAPI)->getBatch($barcode)

			]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
			echo json_encode([$barcode]);
		}
	}

	public function saveUnpick()
	{
		$barcode = filter_input(INPUT_POST, 'barcode');
		$pid = filter_input(INPUT_POST, 'pid');
		$inventTransId = filter_input(INPUT_POST, 'inventTransId');
		$LineID = filter_input(INPUT_POST, 'LineID');

		if ((new BarcodeService)->isBarcodeFoilNull($barcode) === true) {
			$barcode_type = 'BarcodeFoil';
		} else {
			$barcode_type = 'Barcode';
		}

		if ($barcode_type === 'BarcodeFoil') {
			$barcode = (new BarcodeService)->getBarcodeFromBarcodeFoil($barcode);
		}

		if ((new BarcodeService)->isRanged($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		if ((new BarcodeService)->isExistInventTable($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Barcode"]));
		}

		// if ((new InventService)->isReceived($barcode) === false) {
		// 	exit(json_encode(["status" => 404, "message" => "Barcode number status not Received."]));
		// }

		if ((new InventService)->checkWarehouseReceiveData($barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ Recived โดย warehouse."]));
		}

		if ((new LoadingService)->isItemMatch($pid, $inventTransId, $barcode) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่พบ Item number ใน Picking List."]));
		}

		if ((new LoadingService)->isPicked($barcode, $pid) === false) {
			exit(json_encode(["status" => 404, "message" => "Barcode ยังไม่ได้ Picked หรือ Barcode ไม่ถูกต้อง."]));
		}

		$result = (new LoadingService)->saveUnpick($pid, $inventTransId, $barcode, $LineID);

		if ($result === 200) {
			// exit(json_encode(["status" => 200, "message" => "Update Successful!"]));
			exit(json_encode([
				"status" => 200,
				"message" => $barcode,
				"curecode" => (new BarcodeAPI)->getCureCode($barcode),
				'batch' => (new BarcodeAPI)->getBatch($barcode)

			]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
			echo json_encode([$barcode]);
		}
	}

	public function isCustomRemainder()
	{
		$pid = filter_input(INPUT_POST, 'pid');
		$result = (new LoadingService)->isCustomRemainder($pid);
		echo json_encode(['status' => $result]);
	}

	public function confirm()
	{
		$pickingListId = filter_input(INPUT_POST, 'pid');
		$isCustomRemainder = filter_input(INPUT_POST, 'isCustomRemainder');

		$result = (new LoadingService)->confirm($pickingListId, $isCustomRemainder);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Confirm Successful!"]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
		}
	}

	public function forceConfirm()
	{
		$pickingListId = filter_input(INPUT_POST, 'pid');
		$authorize_code = filter_input(INPUT_POST, 'code');
		$authorize_password = filter_input(INPUT_POST, 'password');

		if ((new UserService)->isAuthorize($authorize_code, $authorize_password, 'Loading') === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		$result = (new LoadingService)->forceConfirm($pickingListId, $authorize_code);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Confirm Successful!"]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
		}
	}

	public function cancel()
	{
		$pid = filter_input(INPUT_POST, 'pid');
		$user = filter_input(INPUT_POST, 'user');
		$pass = filter_input(INPUT_POST, 'pass');
		$type = filter_input(INPUT_POST, 'type');

		if ((new UserService)->isAuthorize($user, $pass, $type) === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		$result = (new LoadingService)->cancel($pid);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Cancel Successful!"]));
		} else {
			exit(json_encode(["status" => 400, "message" => 'Cancel Failed!']));
		}
	}

	public function addRemainder()
	{
		$new_remainder = filter_input(INPUT_POST, 'new_remainder');
		$authorize_code = filter_input(INPUT_POST, 'authorize_code');
		$authorize_password = filter_input(INPUT_POST, 'authorize_password');
		$inventTransId = filter_input(INPUT_POST, 'inventTransId');

		if ($this->user->isExist($authorize_code, $authorize_password) === false) {
			exit(json_encode(["status" => 404, "message" => "ไม่มี User ในระบบ"]));
		}

		if ($this->user->isAuthorize($authorize_code, $authorize_password, 'Loading') === false) {
			exit(json_encode(["status" => 404, "message" => "User ไม่มีสิทธิ์อนุมัติ"]));
		}

		$result = (new LoadingService)->addRemainder($new_remainder, $inventTransId, $authorize_code);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "เพิ่ม Remainder สำเร็จ!"]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
		}
	}

	public function loadingTrans($pid, $itemid, $inventtranid)
	{
		echo (new LoadingService)->loadingTrans($pid, $itemid, $inventtranid);
	}

	public function getPickingListByOrderId($order_id)
	{
		echo (new LoadingService)->getPickingListByOrderId($order_id);
	}

	public function savePickingListRef()
	{
		$pickinglist_id_current = filter_input(INPUT_POST, 'pickinglist_id_current');
		$pickinglist_id_ref = filter_input(INPUT_POST, 'pickinglist_id_ref');

		$result = (new LoadingService)->savePickingListRef($pickinglist_id_current, $pickinglist_id_ref);

		if ($result === 200) {
			exit(json_encode(["status" => 200, "message" => "Save Successful!"]));
		} else {
			exit(json_encode(["status" => 400, "message" => $result]));
		}

		# code...
	}
}
