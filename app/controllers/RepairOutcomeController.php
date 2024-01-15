<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\RepairOutcomeService;

class RepairOutcomeController
{
    public function index()
    {
        renderView("page/repair_outcome");
    }

    public function save()
    {
        $barcode = filter_input(INPUT_POST, "barcode");

        if ((new BarcodeService)->isRepairCheck($barcode) === false) {
            return json_encode([
                'result' => false,
                'message' => "Barcode ไม่ถูกต้อง หรือยังไม่ได้ Repair"
            ]);
        }

        // if ((new RepairOutcomeService)->isBarcodeRepairOutcome($barcode) === true) {
        //     return json_encode([
        //         'result' => false,
        //         'message' => "Barcode Number มีการ Repair Outcome ไปแล้ว"
        //     ]);
        // }

        $result = (new RepairOutcomeService)->saveRepairOutcome($barcode);

        if ($result === true) {
            return json_encode([
                "result" => true,
                "message" => 'บันทึก Repair Outcome สำเร็จ'
            ]);
        } else {
            return json_encode([
                "result" => false,
                "message" => 'บันทึก Repair Outcome ไม่สำเร็จ'
            ]);
        }
    }
}
