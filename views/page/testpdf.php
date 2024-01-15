<?php ob_start();
$pdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 2);
$pdf->AliasNbPages();
$pdf->SetFont('Tahoma', 'B', 14);
?>
<?php
$all_time = '';
foreach ($timeset as $v) {
    preg_match_all('(..:..)', $v, $_t);
    foreach ($_t as $x) {
        $all_time .= $x[0] . '-' . $x[1] . ', ';
    }
    // $temp_time = explode(' AND ', $v);
    // $all_time .= substr(substr($temp_time[0], 11), 0, 6) . ' -' . substr(substr($temp_time[1], 11), 0, 6) . ', ';
}

$css = '<html><head><style type="text/css">
    table {
    border-collapse: collapse;
    width: 100%;
    border: 1px solid #000000;
    }

    td, tr, th {
    border: 1px solid #000000;
    text-align: left;
    padding: 13px;
    }

</style>
</head></html>';
$classcss = '<html><head><style type="text/css">
    .table {
    border: 0x;
    border-collapse: collapse;
    width: 100%;
    font-size: 8px;
    }

    .td, .tr, .th {
        border: 0x solid #000000;
        text-align: left;
        padding: 8px;
    }

    .td-d{
        text-align: left;
        padding: 8px;
    }

    .hh {
        display: inline-block;
        padding-left: 300px;
        font-size: 1em;;
    }
</style>
</head></html>';

$datajson = json_decode($rows);

$pages_count = 0;
$pages_num   = [];
$totalPageState = 0;
$tmpQTy = 0;
$count_row = 0;

$setOf_14 = [];
$tmp_setOf_14 = 0;
for ($i = 0; $i < 20; $i++) {
    $tmp_setOf_14 += 12;
    $setOf_14[] = $tmp_setOf_14;
}

foreach ($datajson as $v) {

    $pages_count++;
    $tmpQTy += $v->QTY;

    if ($pages_count % 12 === 0) {
        $pages_num[] = $tmpQTy;
        $tmpQTy = 0;
    }

    if (count($datajson) === $pages_count) {
        $pages_num[] = $tmpQTy;
        $tmpQTy = 0;
    }
}


$pdf->WriteHTML($css);
$pdf->WriteHTML($classcss);
$pdf->WriteHTML("<table border='1' style='font-size: 12px; padding:5px;'>");
$pdf->WriteHTML("
<thead>
<tr>
    <td style='text-align: center;' colspan='2'>
        <img src='./assets/images/str.jpg' width='150' alt=''>
    </td>
    <td style='text-align: center; padding: 30px;' colspan='4'>
        <div>SIAMTRUCK RADIAL CO. LTD.</div>
        <div>Final Send To Warehouse</div>
    </td>

</tr>
<tr>
    <td colspan='6' style='padding: 10px;'>
        Date :$date
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Shift :$shift 
    </td>
</tr>
<tr>
    <th style='border-top: 0px; padding: 10px;'>Truck</th>
    <th style='border-top: 0px; padding: 10px;'>Item No.</th>
    <th style='border-top: 0px; padding: 10px;'>Item Name.</th>
    <th style='border-top: 0px; padding: 10px;'>CureCode</th>
    <th style='border-top: 0px; padding: 10px;'>Batch No.</th>
    <th style='border-top: 0px; padding: 10px;'>QTY</th>

</tr>
</thead>
");

$t = 0;
$round = "";
$descjour = "";
foreach ($data as $key => $value) {

    //    if ($totalPageState === 0) {
    //         $pdf->WriteHTML("
    //             <tr>
    //                 <th colspan=5 style='font-size: 9.5px;'>
    //                     วันที่ : " . $date . " กะ : " . $shift . " BOI : " . $BOIName . " 
    //                     <br> เวลา : " . str_replace(' ', '', trim($all_time, ', '))  . "
    //                     <br> ผู้ตรวจสอบ : ________________________(คลังสินค้า) ผู้ส่ง : ______________________  ผู้รับ : _____________________
    //                 </th>
    //                 <th colspan='3' style=text-align:center;>" . $pages_num[$count_row] . "</th>
    //             </tr>
    //             <tr>
    //                 <th width='7%'><i><u>Item No.</u></i></th>
    //                 <th><i><u>Size</u></i></th>
    //                 <th width='9.8%'><i><u>Pattern</u></i></th>
    //                 <th width='6%'><i><u>Brand</u></i></th>
    //                 <th width='7%'><i><u>Location</u></i></th>
    //                 <th width='7%'><i><u>จำนวน</u></i></th>
    //                 <th width='10%' style='font-family: Tahoma;'><i><u>Weekly</u></i></th>
    //                 <th width='7%' style='font-family: Tahoma;'><i><u>Remark</u></i></th>
    //             </tr>
    //         ");
    //     }

    //     if (in_array($totalPageState, $setOf_14)) {
    //         $count_row++;
    //         $pdf->WriteHTML("
    //             <tr>
    //                 <th colspan=5 style='font-size: 9.5px;'>
    //                     วันที่ : " . $date . " กะ : " . $shift . " BOI : " . $BOIName . " 
    //                     <br> เวลา : " . str_replace(' ', '', trim($all_time, ', '))  . "
    //                     <br> ผู้ตรวจสอบ : ________________________(คลังสินค้า) ผู้ส่ง : ______________________  ผู้รับ : _____________________
    //                 </th>
    //                 <th colspan='3' style=text-align:center;>" . $pages_num[$count_row] . "</th>
    //             </tr>
    //             <tr>
    //                 <th width='7%'><i><u>Item No.</u></i></th>
    //                 <th><i><u>Size</u></i></th>
    //                 <th width='9.8%'><i><u>Pattern</u></i></th>
    //                 <th width='6%'><i><u>Brand</u></i></th>
    //                 <th width='7%'><i><u>Location</u></i></th>
    //                 <th width='7%'><i><u>จำนวน</u></i></th>
    //                 <th width='10%' style='font-family: Tahoma;'><i><u>Weekly</u></i></th>
    //                 <th width='7%' style='font-family: Tahoma;'><i><u>Remark</u></i></th>
    //             </tr>
    //         ");
    //     }
    // if ($round != $value["TruckID"] || $descjour != $value["descjour"]) {
    $pdf->WriteHTML("

        <tr>
        
        <td align='left'>" . $value["TruckID"] . "</td>
        <td align='left'>" . $value["ItemID"] . "</td>
        <td align='center'>" . $value["NameTH"] . "</td>
        <td align='center'>" . $value["ID"] . "</td>
        <td align='center'>" . $value["Batch"] . "</td>
        <td align='center'>" . $value["qty"] . "</td>

        </tr>
    ");
    // }
    // $round = $value["TruckID"];
    // $descjour = $value["descjour"];


    //     $totalPageState++;
    //     $pdf->SetHTMLFooter('
    //         <table class="table">
    //             <tr class="tr">
    //                 <td class="td" align="left">
    //                     Ref.WI-SR-21.1
    //                 </td>
    //                 <td class="td" align="right">
    //                     FM-SR-2.1.1, Issued #3
    //                 </td>
    //             </tr>
    //         </table>
    //     ');
}
$pdf->WriteHTML("</table>");



// $pdf->WriteHTML(count($pages_num));
$pdf->Output();
