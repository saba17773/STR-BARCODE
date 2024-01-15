<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=BuildingRate_Report_Report_" . Date("Ymd_His") . ".xls");

?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Building Rate Report</title>
    <!-- 
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 8px;
            font-family: "Angsana New";
        }

        td,
        tr,
        th {
            border: 1px solid #000000;
            text-align: center;
            padding: 4px;

        }

        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 8px;
        }

        .td,
        .tr,
        .th {
            border: 0px solid #000000;
            text-align: left;
            padding: 4px;
        }

        .double_td {
            border: 2px solid black;
        }

        .f12 {
            font-size: 12px;
            font-family: "Angsana New";
        }

        .f10 {
            font-size: 10px;
            font-family: "Angsana New";

        }
    </style> -->
</head>

<body>
    <div class="container">
        <table border="1">
            <thead>
                <tr>
                    <td style="text-align: center;" colspan="2">
                        <!-- <img src="./assets/images/str.jpg" width="100" alt=""> -->
                        <!-- <img src="\\lanister\str-barcode\assets\images\str.jpg" width="100" alt=""> -->
                         <img src="http://lanister.deestonegrp.com:3311/assets/images/str.jpg" width="100" alt="">
                    </td>
                    <td align="center" colspan="11" style="font-size:14px">
                        <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Building Rate Report</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="13" class="f12"><br />
                        <b>DATE : <?php echo $date; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>SHIFT : <?php echo $shift; ?></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Export Date : <?php echo $export_date; ?></b>
                    </td>
                </tr>
            </thead>

            <?php
            $machine = "";
            foreach ($data as $key => $value) {
                if ($value['colspan'] == "13") {
                    echo "<tr>";
                    echo "<td colspan=" . $value['colspan'] . " class='f12' width='100%'>-- ไม่มีข้อมูล --</td>";
                    echo "</tr>";
                } else {

                    if ($machine != $value["Machine"]) {
                        echo "<tr>";
                        echo "<td rowspan='2' class='f12' width='6%'>เครื่อง</td>";
                        echo "<td rowspan='2' class='f12' width='12%'>รหัสพนักงาน</td>";
                        echo "<td rowspan='2' class='f12' width='15%'>ชื่อ-นามสกุล</td>";
                        echo "<td rowspan='2' class='f12' width='9%'>ตำแหน่ง</td>";
                        echo "<td rowspan='2' class='f12' width='5%'>SCH.</td>";
                        echo "<td rowspan='2' class='f12' width='5%'>Act.</td>";
                        echo "<td class='f12' colspan='3' width='24%'>Rate Build (Tire)</td>";
                        echo "<td class='f12' width='6%'>ยอดเงิน</td>";
                        echo "<td class='f12' width='6%'>ยอดหัก</td>";
                        echo "<td class='f12' width='6%'>ยอดสุทธิ</td>";
                        echo "<td class='f12' width='6%'>รวม</td>";
                        echo "</tr>";
                    }



                    if ($machine != $value["Machine"]) {
                        echo "<tr>";
                        echo "<td class='f12'>" . $value["Qty1"] . "</td>";
                        echo "<td class='f12'>" . $value["Qty2"] . "</td>";
                        echo "<td class='f12'>" . $value["Qty3"] . "</td>";
                        echo "<td class='f12'>(บาท)</td>";
                        echo "<td class='f12'>(บาท)</td>";
                        echo "<td class='f12'>(บาท)</td>";
                        echo "<td class='f12'>(บาท)</td>";
                        echo "</tr>";
                    }


                    echo "<tr>";
                    if ($machine != $value["Machine"]) {

                        echo "<td class='f12' rowspan=" . $value['rowspan'] . ">" . $value["Machine"] . "</td>";
                    }

                    echo "<td class='f12' height='23px' >" . "&nbsp;".$value["EmployeeID"] . "</td>";
                    echo "<td class='f12'>" . $value["Name"] . "</td>";
                    echo "<td class='f12'>" . $value["BuildType"] . "</td>";

                    if ($machine != $value["Machine"]) {
                        echo "<td class='f12' rowspan=" . $value['rowspan'] . ">" . $value["SCH"] . "</td>";
                    }

                    echo "<td class='f12'>" . $value["Act"] . "</td>";
                    echo "<td class='f12'>" . $value["P1"] . "</td>";
                    echo "<td class='f12'>" . $value["P2"] . "</td>";
                    echo "<td class='f12'>" . $value["P3"] . "</td>";
                    echo "<td class='f12'>" . $value["Total"] . "</td>";
                    echo "<td class='f12'>" . $value["Charge"] . "</td>";
                    echo "<td class='f12'>" . $value["Total_Diff"] . "</td>";

                    if ($machine != $value["Machine"]) {
                        echo "<td class='f12' rowspan=" . $value['rowspan'] . ">" . $value["Sum_Total"] . "</td>";
                    }
                    //V3
                    // if ($machine != $value["Machine"]) 
                    // {
                    //     //".$value["Sum_Total"]."
                    //     foreach ($data2 as $key => $value2) 
                    //     {
                    //         if($value["Machine"] === $value2["Machine"])
                    //         {
                    //             echo "<td class='f12' rowspan=".$value['rowspan']."> ".$value2["Sum_Total"]." </td>";
                    //         } 
                    //     }

                    // }

                    echo "</tr>";
                    $machine = $value["Machine"];
                }
                // echo $value['Boiler'].$value['ItemID'];
                // echo "<br>";

            }
            ?>

        </table>

        <br><br>
        <b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            ลงชื่อ : .................................................. (หัวหน้ากลุ่ม) 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
            ลงชื่อ : .................................................. (หัวหน้าแผนก)
        </b>

    </div>
</body>


</html>
<?
$html = ob_get_contents();
ob_end_clean();
