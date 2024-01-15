<?php

namespace App\Controllers;

use App\Services\ScheduleService;
use App\Components\Database;

class ScheduleController
{
    public function bindGrid($shift, $date)
    {
        echo (new ScheduleService)->bindGrid($shift, $date);
    }

    public function bindGridLine($machine, $datesch, $shift)
    {
        $dateinter = date('Y-m-d', strtotime($datesch));

        echo (new ScheduleService)->bindGridLine($machine, $dateinter, $shift);
    }

    public function BuildSchedule_V2()
    {
        $date_sch = filter_input(INPUT_POST, "date_sch");
        $shift          = filter_input(INPUT_POST, "shift");
        $date_inter = date('Y-m-d', strtotime($date_sch));

        // echo "<pre>".print_r($date,true)."</pre>";
        // echo "<pre>".print_r($shift,true)."</pre>";
        // exit();

        renderView("page/rate_buildsch_v2", [
            "date" => $date_sch,
            "shift" => $shift,
            "date_inter" => $date_inter
        ]);
    }

    public function downloadBuildSchedule($date_inter, $shift)
    {
        // renderView("page/download_schbuild");
        renderView("page/download_schbuild", [
            "date_inter" => $date_inter,
            "shift" => $shift
        ]);
    }

    public function CureSchedule_V2()
    {
        $date_sch = filter_input(INPUT_POST, "date_sch");
        // $shift          = filter_input(INPUT_POST, "shift");
        $date_inter = date('Y-m-d', strtotime($date_sch));

        // echo "<pre>".print_r($date,true)."</pre>";
        // echo "<pre>".print_r($shift,true)."</pre>";
        // exit();

        renderView("page/cure_sch_v2", [
            "date" => $date_sch,
            //"shift" => $shift,
            "date_inter" => $date_inter
        ]);
    }

    public function downloadBuildSchedule1($date_inter, $shift)
    {
        // renderView("page/download_schbuild");
        renderView("page/download_schcure", [
            "date_inter" => $date_inter,
            "shift" => $shift
        ]);
    }

    public function cureGrid($shift, $date)
    {
        echo (new ScheduleService)->cureGrid($shift, $date);
    }
}
