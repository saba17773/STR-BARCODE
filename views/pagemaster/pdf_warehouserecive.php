<?php ob_start();
$pdf = new mPDF('th', 'A4-L', 0, '', 5, 5, 5, 10);
$pdf->AliasNbPages();
// $pdf->SetFont('Tahoma','B',14);
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
            <th colspan=5 style='font-size: 2em; ' >
                <img src=./assets/images/STR.jpg style='height:50px; width:auto;' />
              ใบรายงาน รับสินค้าเข้าคลังสินค้า 
            </th>
            <th colspan=3 style=text-align:center;>Grand Total</th>
        </tr>
    </thead>
");

$t = 0;
foreach ($datajson as $v) {

    if ($totalPageState === 0) {
        $pdf->WriteHTML("
            <tr>
                <th colspan=5 style='font-size: 9.5px;'>
                    วันที่ : " . $date . " กะ : " . $shift . " BOI : " . $BOIName . " 
                    <br> เวลา : " . str_replace(' ', '', trim($all_time, ', '))  . "
                    <br> ผู้ตรวจสอบ : ________________________(คลังสินค้า) ผู้ส่ง : ______________________  ผู้รับ : _____________________
                </th>
                <th colspan='3' style=text-align:center;>" . $pages_num[$count_row] . "</th>
            </tr>
            <tr>
                <th width='7%'><i><u>Item No.</u></i></th>
                <th><i><u>Size</u></i></th>
                <th width='9.8%'><i><u>Pattern</u></i></th>
                <th width='6%'><i><u>Brand</u></i></th>
                <th width='7%'><i><u>Location</u></i></th>
                <th width='7%'><i><u>จำนวน</u></i></th>
                <th width='10%' style='font-family: Tahoma;'><i><u>Weekly</u></i></th>
                <th width='7%' style='font-family: Tahoma;'><i><u>Remark</u></i></th>
            </tr>
        ");
    }

    if (in_array($totalPageState, $setOf_14)) {
        $count_row++;
        $pdf->WriteHTML("
            <tr>
                <th colspan=5 style='font-size: 9.5px;'>
                    วันที่ : " . $date . " กะ : " . $shift . " BOI : " . $BOIName . " 
                    <br> เวลา : " . str_replace(' ', '', trim($all_time, ', '))  . "
                    <br> ผู้ตรวจสอบ : ________________________(คลังสินค้า) ผู้ส่ง : ______________________  ผู้รับ : _____________________
                </th>
                <th colspan='3' style=text-align:center;>" . $pages_num[$count_row] . "</th>
            </tr>
            <tr>
                <th width='7%'><i><u>Item No.</u></i></th>
                <th><i><u>Size</u></i></th>
                <th width='9.8%'><i><u>Pattern</u></i></th>
                <th width='6%'><i><u>Brand</u></i></th>
                <th width='7%'><i><u>Location</u></i></th>
                <th width='7%'><i><u>จำนวน</u></i></th>
                <th width='10%' style='font-family: Tahoma;'><i><u>Weekly</u></i></th>
                <th width='7%' style='font-family: Tahoma;'><i><u>Remark</u></i></th>
            </tr>
        ");
    }

    $pdf->WriteHTML("
    
        <tr>
            <td width='7%' class='td-d'>" . isExists($v->ItemID) . "</td>
            <td class='td-d'>" . $v->NameTH . "</td>
            <td width='9.8%'>" . $v->Pattern . "</td>
            <td width='6%'>" . $v->Brand . "</td>
            <td width='7%'> </td>
            <td width='7%'>" . $v->QTY . "</td>
            <td width='10%'>" . $v->Batch . "</td>
            <td width='7%'> </td>
        </tr>
    ");

    $totalPageState++;
    $pdf->SetHTMLFooter('
        <table class="table">
            <tr class="tr">
                <td class="td" align="left">
                    Ref.WI-SR-21.1
                </td>
                <td class="td" align="right">
                    FM-SR-2.1.1, Issued #3
                </td>
            </tr>
        </table>
    ');
}
$pdf->WriteHTML("</table>");


/*foreach ($pages_num as $page) {
    $rows = calPage($datajson, $page);
    $total = calTotal($datajson, $page);
    $pdf->WriteHTML($css);
    $pdf->WriteHTML($classcss);
    //$pdf->WriteHTML("Total : " . $total ."<br>");
    $pdf->WriteHTML("
        <table>
            <tr>
                <td>
                    <a class=navbar-brand>
                        <img src=./assets/images/STR.jpg style=padding-left:10px;height:30px; width:auto; />
                    </a>
                </td>
                <td width=50%>
                    <h2>ใบรายงาน รับสินค้าเข้าคลังสินค้า</h2>
                </td>
                <td>
                    Grand Total
                </td>
            </tr>
            <tr>
                <td colspan=2 align=left>วันที่ : ".$date." เวลา : ".str_replace(' ', '', trim($all_time, ', '))." กะ : ".$shift."
                <br><br>ผู้ตรวจสอบ : ________________________(คลังสินค้า) ผู้ส่ง : ______________________  ผู้รับ : _____________________
                </td>
                <td>".$total."</td>
                </tr></table>
                <table><tr>
                <td width='10%'><i><u>Item No.</u></i></td>
                <td width='30%'><i><u>Size</u></i></td>
                <td width='10%'><i><u>Pattern</u></i></td>
                <td width='10%'><i><u>Brand</u></i></td>
                <td width='10%'><i><u>Location</u></i></td>
                <td width='10%'><i><u>จำนวน</u></i></td>
                <td width='10%' style='font-family: Tahoma;'><i><u>Weekly</u></i></td>
                <td width='10%' style='font-family: Tahoma;'><i><u>Remark</u></i></td>
                </tr>");
    foreach ($rows as $v) {
        //$pdf->WriteHTML($v->Pages ."-".$v->ItemID .  "<br>");
        // echo '<pre>'. print_r($v, true) . '</pre>';
        $pdf->WriteHTML("<tr>
        <td width='10%' class='td-d'>".isExists($v->ItemID)."</td>
        <td width='50%' class='td-d'>".$v->NameTH."</td>
        <td width='5%'>".$v->Pattern."</td>
        <td width='5%'>".$v->Brand."</td>
        <td width='5%'> </td>
        <td width='7%'>".$v->QTY."</td>
        <td width='5%'>".$v->Batch."</td>
        <td width='7%'> </td>
        </tr>");
    }

    $pdf->WriteHTML("</table>");
    $pdf->SetHTMLFooter('<table class="table"><tr class="tr">
            <td class="td" align="left">
                Ref.WI-SR-21.1
            </td>
            <td class="td" align="right">
                FM-SR-2.1.1, Issued #3
            </td>
        </tr>
        </table>');

    // $pdf->AddPage();
    if (count($pages_num) > $page) {
       $pdf->AddPage();
    }

}*/

function isExists($item)
{
    if ($item === null || $item === "") {
        return "<br><div></div><br>";
    } else {
        return $item;
    }
}

function  calPage($data, $page)
{
    $temp = [];
    foreach ($data as $v) {
        if ($v->Pages === $page) {
            $temp[] = $v;
        }
    }
    return $temp;
}

function calTotal($data, $page)
{
    $total = 0;
    foreach ($data as $v) {
        if ($v->Pages === $page) {
            $total += $v->QTY;
        }
    }
    return $total;
}
// $pdf->WriteHTML(count($pages_num));
$pdf->Output();
