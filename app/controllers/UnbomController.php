<?php

namespace App\Controllers;

use App\Components\Utils;
use App\Components\Database as DB;
use App\Services\BomService;
use App\Services\BarcodeService;
use App\Services\InventService;
use App\Libs\InventTable as InventLibs;

class UnbomController
{
    public function index()
    {
        renderView("page/unbom");
    }

    public function saveUnbom()
    {
        $barcode = filter_input(INPUT_POST, 'barcode');
        $authorize_by = filter_input(INPUT_POST, 'authorize');

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

        if ((new BarcodeService)->isReceived($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => (new BarcodeService)->getBarcodeStatus($barcode)
            ]);
        }

        if ((new InventService)->checkWarehouseReceiveDate($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => 'Barcode ยังไม่ได้รับเข้า Warehouse'
            ]);
        }

        if ((new BomService)->isBarcodeBomExist($barcode) === false) {
             return json_encode([
                'result' => false,
                'message' => 'Barcode Number ยังไม่ได้ BOM'
            ]);
        }

        if ((new InventLibs)->isRefItemExists($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => 'Barcode นี้ไม่มี Reference Item'
            ]);
        }

        $result = (new BomService)->saveUnbom($authorize_by, $barcode);

        if ($result === true) {
            return json_encode([
                "result" => true,
                "message" => 'Unbom สำเร็จ'
            ]);
        } else {
            return json_encode([
                "result" => false,
                "message" => $result
            ]);
        }
    }
}