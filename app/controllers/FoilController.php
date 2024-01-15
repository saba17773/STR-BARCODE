<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\InventService;
use App\Services\FoilService;
use App\Components\Utils;
use App\Components\Database as DB;

class FoilController
{
	public function renderFoil()
	{
		renderView('page/foil');
	}

	public function renderUnfoil()
	{
		renderView('page/unfoil');
	}

	public function saveFoil()
	{
		$old_barcode = filter_input(INPUT_POST, 'old_barcode');
		$new_barcode = filter_input(INPUT_POST, 'new_barcode');

        if (!isset($_SESSION['user_login'])) {
            return json_encode([
                "result" => false,
                "message" => "Login Error , กรุณา Login ใหม่อีกครั้ง"
            ]);
		
		}

        if ((new BarcodeService)->isRanged($old_barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => "Barcode ไม่ถูกต้อง"
            ]);
        }
    
        if ((new BarcodeService)->isExistInventTable($old_barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => "ไม่พบ Barcode"
            ]);
        }

        if ((new FoilService)->isBarcodeFoilUsed($new_barcode) === true) {
            return json_encode([
                "result" => false,
                "message" => "Barcode Foil ถูกใช้ไปแล้ว"
            ]);
        }

        if ((new InventService)->isReceived($old_barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => (new BarcodeService)->getBarcodeStatus($old_barcode)
            ]);
        }
        
        if ((new InventService)->checkWarehouseReceiveDate($old_barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => "Barcode ยังไม่ได้รับเข้า Warehouse"
            ]);
        }

        if ((new FoilService)->isBarcodeFoilNull($old_barcode) === false) {
        	return json_encode([
              "result" => false,
              "message" => "Barcode Number มีการ Foil ไปแล้ว" 
          ]);
        }

        if ((new BarcodeService)->isRanged($new_barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => "New Barcode ไม่ถูกต้อง"
            ]);
        }

        if ((new BarcodeService)->isExistInventTable($new_barcode) === true) {
          return json_encode([
              "result" => false,
              "message" => "Barcode นี้ติดอยู่ที่ยางแล้วไม่สามารถนำมาติดที่ foil ได้อีก"
          ]);
        }

        if ((new FoilService)->isBarcodeFoilNull($new_barcode) === true) {
        	return json_encode([
              "result" => false,
              "message" => "New barcode มีการใช้งานไปแล้ว"
            ]);
        }

        $result = (new FoilService)->saveFoil($old_barcode, $new_barcode);

        if ($result === true) {
            return json_encode([
              "result" => true,
              "message" => "Foil สำเร็จ"
            ]);
        } else {
            return json_encode([
              "result" => false,
              "message" => $result
            ]);
        }
	}

	public function saveUnfoil()
	{
        // return json_encode([
        //     "result" => false,
        //     "message" => "ปิดปรับปรุงชั่วคราว"
        // ]);

		$barcode = filter_input(INPUT_POST, 'barcode');

		if ((new BarcodeService)->isRanged($barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => "Barcode ไม่ถูกต้อง"
            ]);
        }

        if ((new FoilService)->isBarcodeFoilExists($barcode) === false) {
            return json_encode([
              "result" => false,
              "message" => "ไม่พบ Barcode Foil" 
          ]);
        }

        if ((new FoilService)->isBarcodeFoilStatusReceive($barcode) === false) {
            return json_encode([
                "result" => false,
                "message" => 'Barcode status : ' . (new BarcodeService)->getBarcodeStatus($barcode)
            ]);
        }

        // ==
        $result = (new FoilService)->saveUnfoil($barcode);

        if ($result === true) {
            return json_encode([
              "result" => true,
              "message" => "Unfoil สำเร็จ"
            ]);
        } else {
            return json_encode([
              "result" => false,
              "message" => $result
            ]);
        }
	}

    public function reportFoil()
    {
        renderView('report/foil');
    }

    public function reportFoilPDF()
    {
        $date = filter_input(INPUT_POST, "date");
        $shift = filter_input(INPUT_POST, "shift");
        $time_selected = $_POST["selecttime"];
        

        $datewarehouse = date('Y-m-d', strtotime($date));
        $datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));       

        $timeset = [];

        foreach ($time_selected as $k => $time_id) {
            // echo $time_id;
            if ($time_id === '1') {
                    array_push($timeset, '\'' . $datewarehouse . ' 08:00:00\' AND ' . '\'' . $datewarehouse . ' 10:00:00\'');
            }   else if ($time_id === '2') {
                    array_push($timeset, '\'' . $datewarehouse . ' 10:00:00\' AND ' . '\'' . $datewarehouse . ' 12:00:00\'');
            }   else if ($time_id === '3') {
                    array_push($timeset, '\'' . $datewarehouse . ' 12:00:00\' AND ' . '\'' . $datewarehouse . ' 14:00:00\'');
            }   else if ($time_id === '4') {
                    array_push($timeset, '\'' . $datewarehouse . ' 14:00:00\' AND ' . '\'' . $datewarehouse . ' 16:00:00\'');
            }   else if ($time_id === '5') {
                    array_push($timeset, '\'' . $datewarehouse . ' 16:00:00\' AND ' . '\'' . $datewarehouse . ' 18:00:00\'');
            }   else if ($time_id === '6') {
                    array_push($timeset, '\'' . $datewarehouse . ' 18:00:00\' AND ' . '\'' . $datewarehouse . ' 20:00:00\'');
            } else if ($time_id === '7') {
                    array_push($timeset, '\'' . $datewarehouse . ' 20:00:00\' AND ' . '\'' . $datewarehouse . ' 22:00:00\'');
            }   else if ($time_id === '8') {
                    array_push($timeset, '\'' . $datewarehouse . ' 22:00:00\' AND ' . '\'' .$datewarehouse_nextday . ' 00:00:00\'');
            }   else if ($time_id === '9') {
                    array_push($timeset, '\'' . $datewarehouse_nextday . ' 00:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 02:00:00\'');
            }   else if ($time_id === '10') {
                    array_push($timeset, '\'' . $datewarehouse_nextday . ' 02:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 04:00:00\'');
            }   else if ($time_id === '11') {
                    array_push($timeset, '\'' . $datewarehouse_nextday . ' 04:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 06:00:00\'');
            }   else if ($time_id === '12') {
                    array_push($timeset, '\'' . $datewarehouse_nextday . ' 06:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 08:00:00\'');
            }
        }
        // var_dump($timeset); exit;
        $FoilService = new FoilService;
        $rows = $FoilService->reportFoil($timeset); 

        renderView('report/report_foil_pdf', [
            'rows' => $rows,
            'timeset' => $timeset,
            'shift' => $shift,
            'date' => $datewarehouse
        ]);
    }
}