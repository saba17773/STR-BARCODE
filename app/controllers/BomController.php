<?php

namespace App\Controllers;

use App\Models\Bom;
use App\Services\BarcodeService;
use App\Services\ItemSetService;
use App\Services\InventService;
use App\Services\BomService;

class BomController
{
    public function index()
    {
        renderView("page/bom");
    }

    public function save()
    {
        $item_id = filter_input(INPUT_POST, "item_id");
        $barcode = filter_input(INPUT_POST, "barcode");

        if ((new ItemSetService)->isItemSetExists($item_id) === false) {
            return json_encode([
                'result' => false,
                'message' => "Item Set ไม่มีอยู่ในระบบ"
            ]);
        }

        if ((new BarcodeService)->isRanged($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => 'Barcode ไม่ถูกต้อง'
            ]);
        }

        if ((new BarcodeService)->isExistInventTable($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => 'ไม่พบ Barcode'
            ]);
        }

        if ((new InventService)->isReceived($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => (new BarcodeService)->getBarcodeStatus($barcode)
            ]);
        }

        if ((new InventService)->checkWarehouseReceiveDate($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => "Barcode ยังไม่ได้รับเข้า Warehouse"
            ]);
        }

        if ((new BomService)->isBarcodeBomExist($barcode) === true) {
            return json_encode([
                'result' => false,
                'message' => "Barcode Number มีการ BOM ไปแล้ว"
            ]);
        }

        $item_id_by_item_set = (new ItemSetService)->getItemIdFromItemSet($item_id);
        $barcode_detail = (new BarcodeService)->getBarcodeInfoV2($barcode);

        if (count($barcode_detail) === 0) {
            return json_encode([
                'result' => false,
                'message' => 'can\'t get barcode detail.'
            ]);
        }

        if (count($item_id_by_item_set) === 0) {
            return json_encode([
                'result' => false,
                'message' => 'can\'t get item id by item set.'
            ]);
        }

        if (!isset($item_id_by_item_set[0]['item_id']) || $item_id_by_item_set[0]['item_id'] === "") {
            return json_encode([
                'result' => false,
                'message' => "Item ID From Item Set Not found"
            ]);
        }

        if ($item_id_by_item_set[0]['item_id'] !== $barcode_detail[0]['ItemID']) {
            return json_encode([
                'result' => false,
                'message' => "Item ID ไม่ตรงกัน"
            ]);
        }

        $result = (new BomService)->saveBom($item_id, $barcode);

        if ($result === true) {
            return json_encode([
                "result" => true,
                "message" => 'Bom Successful'
            ]);
        } else {
            return json_encode([
                "result" => false,
                "message" => $result
            ]);
        }
    }

    public function reportBom()
    {
        renderView('report/bom');
    }

    public function reportBomPDF()
    {
        $date = filter_input(INPUT_POST, "date");
        $shift = filter_input(INPUT_POST, "shift");
        $time_selected = $_POST["selecttime"];
        // var_dump($_POST);
        $datewarehouse = date('Y-m-d', strtotime($date));
        $datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));

        $timeset = [];

        foreach ($time_selected as $k => $time_id) {
            // echo $time_id;
            if ($time_id === '1') {
                array_push($timeset, '\'' . $datewarehouse . ' 08:00:00\' AND ' . '\'' . $datewarehouse . ' 10:00:00\'');
            } else if ($time_id === '2') {
                array_push($timeset, '\'' . $datewarehouse . ' 10:00:00\' AND ' . '\'' . $datewarehouse . ' 12:00:00\'');
            } else if ($time_id === '3') {
                array_push($timeset, '\'' . $datewarehouse . ' 12:00:00\' AND ' . '\'' . $datewarehouse . ' 14:00:00\'');
            } else if ($time_id === '4') {
                array_push($timeset, '\'' . $datewarehouse . ' 14:00:00\' AND ' . '\'' . $datewarehouse . ' 16:00:00\'');
            } else if ($time_id === '5') {
                array_push($timeset, '\'' . $datewarehouse . ' 16:00:00\' AND ' . '\'' . $datewarehouse . ' 18:00:00\'');
            } else if ($time_id === '6') {
                array_push($timeset, '\'' . $datewarehouse . ' 18:00:00\' AND ' . '\'' . $datewarehouse . ' 20:00:00\'');
            } else if ($time_id === '7') {
                array_push($timeset, '\'' . $datewarehouse . ' 20:00:00\' AND ' . '\'' . $datewarehouse . ' 22:00:00\'');
            } else if ($time_id === '8') {
                array_push($timeset, '\'' . $datewarehouse . ' 22:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 00:00:00\'');
            } else if ($time_id === '9') {
                array_push($timeset, '\'' . $datewarehouse_nextday . ' 00:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 02:00:00\'');
            } else if ($time_id === '10') {
                array_push($timeset, '\'' . $datewarehouse_nextday . ' 02:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 04:00:00\'');
            } else if ($time_id === '11') {
                array_push($timeset, '\'' . $datewarehouse_nextday . ' 04:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 06:00:00\'');
            } else if ($time_id === '12') {
                array_push($timeset, '\'' . $datewarehouse_nextday . ' 06:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 08:00:00\'');
            }
        }
        // var_dump($timeset); exit;
        $BomService = new BomService;
        $rows = $BomService->reportBom($timeset);

        renderView('report/report_bom_pdf', [
            'rows' => $rows,
            'timeset' => $timeset,
            'shift' => $shift,
            'date' => $datewarehouse
        ]);
    }
}
