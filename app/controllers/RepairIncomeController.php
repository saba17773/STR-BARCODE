<?php

namespace App\Controllers;

use App\Services\BarcodeService;
use App\Services\RepairIncomeService;

class RepairIncomeController
{
    public function index()
    {
        renderView("page/repair_income");
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

        // if ((new RepairIncomeService)->isBarcodeRepairIncome($barcode) === true) {
        //     return json_encode([
        //         'result' => false,
        //         'message' => "Barcode Number มีการ Repair Income ไปแล้ว"
        //     ]);
        // }

        $result = (new RepairIncomeService)->saveRepairIncome($barcode);

        if ($result === true) {
            return json_encode([
                "result" => true,
                "message" => 'บันทึก Repair Income สำเร็จ'
            ]);
        } else {
            return json_encode([
                "result" => false,
                "message" => 'บันทึก Repair Income ไม่สำเร็จ'
            ]);
        }
    }
}
