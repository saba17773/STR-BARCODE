<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>BANBURY Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">
	table {
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
    text-align: center;
}

td, tr {
    border: 1px solid #000000;
    text-align: left;
    padding: 6px;
    text-align: center;
}
.f12{
	font-size:12px;
    font-family:"Angsana New";
}
.table{
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
    align-content: left;
}
.td, .tr {
    border: 1px solid #000000;
    text-align: left;
}
.right{
    font-size: 12px;
    border: 1px solid #000000;
    text-align: right;
    border: 0px;
}
.border0 {
    border: 0px;
    text-align: left;
}

</style>
</head>
<body>

<div class="container">

<table>
    <thead>
    <tr>
        <td  colspan="3">
            <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
            style="height:30px; width:auto;" /></a> 
        </td>
        <td align="center" colspan="22" class="f12">
            <h2><b>ใบสั่งงานและใบรายงานการผสมยาง</b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="25" class="f12" align="left">
            Machine No : 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            วันที่ : <?php echo $date; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;           
            <?php echo "เวลา  : ".$shiftA." น. ถึง ".$shiftB." น."; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Shift : <?php echo strtoupper($mode); ?>
            
        </td>
    </tr>
    <tr>
        <td rowspan="2">เวลา <br> ที่ผสม</td>
        <td rowspan="2" width="6%">สูตรยาง <br> Formula </td>
        <td rowspan="2">จำนวนที่สั่ง <br> Batch</td>
        <td rowspan="2">จำนวนที่ได้ <br> Batch</td>
        <td rowspan="2">ส่วนผสม <br> Ingredients</td>
        <td rowspan="2">วันที่ <br> Date</td>
        <td rowspan="2">น้ำหนัก/Batch <br> (Kg.)</td>
        <td colspan="3">น้ำหนักที่ตรวจสอบ</td>
        <td rowspan="2">น้ำหนักเฉลี่ย/Batch <br> (Kg.)</td>
        <td rowspan="2">Rotor speed <br> ความเร็ว Rotor</td>
        <td rowspan="2">CompoundTemp <br> อุณหภูมิเนื้อยาง</td>
        <td rowspan="2">หมายเหตุ <br>Remark</td>
    </tr>
    <tr>
        <td>Act.1 (Kg.)</td>
        <td>Act.2 (Kg.)</td>
        <td>Act.3 (Kg.)</td>
    </tr>
    </thead>
        <?php $i=1;
        foreach ($databbr as $value) {
            echo "<tr>";
            echo "<td><br></td>";
            echo "<td>".$value->PastCodeID."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->GoodQty."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->ErrorQty."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->GroupName."</td>";
            echo "</tr>";
        $i++; } 
        ?>
</table>


<p class="f12">รายชื่อพนักงาน</p>
<table>
    <tr class="border0">
        <td width="45%" class="border0">
            <table class="table">
                <tr class="tr">
                    <td class="td">1</td>
                    <td class="td">6</td>
                    <td class="td">11</td>
                </tr>
                <tr class="tr">
                    <td class="td">2</td>
                    <td class="td">7</td>
                    <td class="td">12</td>
                </tr>
                <tr class="tr">
                    <td class="td">3</td>
                    <td class="td">8</td>
                    <td class="td">13</td>
                </tr>
                <tr class="tr">
                    <td class="td">4</td>
                    <td class="td">9</td>
                    <td class="td">14</td>
                </tr>
                <tr class="tr">
                    <td class="td">5</td>
                    <td class="td">10</td>
                    <td class="td">15</td>
                </tr>
            </table>
        </td>
        <td class="border0">
            <table class="table">
                <tr class="tr">
                  <td class="td">CAUSE OF DOWN TIME</td>
                </tr>
                <tr class="tr">
                    <td class="td"><br></td>
                </tr>
                <tr class="tr">
                    <td class="td"><br></td>
                </tr>
                <tr class="tr">
                    <td class="td"><br></td>
                </tr>
                <tr class="tr">
                    <td class="td"><br></td>
                </tr>
            </table>   
        </td>
        <td width="30%" class="border0">
            <table class="table">
                <tr class="right">
                    <td class="right">ลงชื่อ________________________หัวหน้ากลุ่ม
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr class="right">
                    <td class="right">ลงชื่อ_______________________หัวหน้าแผนก
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>   
        </td>
    </tr>
</table>
<br>

</div>

</body>
</html>
<?php

$stylecss = "table {
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
    text-align: center;
    }

    td, tr {
        border: 1px solid #000000;
        text-align: left;
        padding: 6px;
        text-align: center;
    }";
$stylecss_tb2 = "table{
        border-collapse: collapse;
        font-family:'Angsana New';
        width: 100%;
        font-size: 12px;
        align-content: left;
    }";
$classcss='<html><head><style type="text/css">
    .td, .tr {
        border: 1px solid #000000;
        text-align: left;
    }
    .right{
        font-size: 12px;
        border: 1px solid #000000;
        text-align: right;
        border: 0px;
    }
    .border0 {
        border: 0px;
        text-align: left;
    }
</style>
</head></html>';
// $header_cmc = "<table>
//         <thead>
//         <tr>
//             <td  colspan='3'>
//                 <a class='navbar-brand'><img  src='./assets/images/STR.jpg' 
//                 style='height:30px; width:auto;' /></a> 
//             </td>
//             <td align='center' colspan='22' class='f12'>
//                 <h2><b>ใบรายงานการชั่งเคมี</b></h2>
//             </td>
//         </tr>
//         <tr>
//             <td colspan='25' class='f12' align='left'>
//                 Chemical : 
//                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
//                 วันที่ : $date
//                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;           
//                 เวลา  : $shiftA  น. ถึง $shiftB น.
//                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
//                 Shift : ".strtoupper($mode)."
//             </td>
//         </tr>
//         <tr>
//             <td rowspan='2' width='6%'>สูตรเคมี (ยาง) </td>
//             <td rowspan='2'>Document <br> No.</td>
//             <td rowspan='2'>Issued <br> No.</td>
//             <td rowspan='2'>จำนวนที่สั่ง <br> Batch</td>
//             <td rowspan='2'>จำนวนที่ได้ <br> Batch</td>
//             <td rowspan='2'>ส่วนผสม <br> Ingredients</td>
//             <td rowspan='2'>วันที่เคมี <br> Date</td>
//             <td rowspan='2'>Spec.น้ำหนัก<br>ต่อ Batch (Kg.)</td>
//             <td colspan='3'>น้ำหนักที่ตรวจสอบ/สูตร(Kg.)</td>
//             <td colspan='2'>เขียน Code เคมีบนถุง</td>
//             <td rowspan='2'>หมายเหตุ <br>Remark</td>
//         </tr>
//         <tr>
//             <td>Act.1 (Kg.)</td>
//             <td>Act.2 (Kg.)</td>
//             <td>Act.3 (Kg.)</td>
//             <td>Pass</td>
//             <td>Reject</td>
//         </tr>
//     </table>";

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);  
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->WriteHTML("
    <table>
        <tr class='border0'>
            <td class='border0' align='left'>
                Ref.WI-PP-2.2
            </td>
            <td class='border0' align='right'>
                FM-PP-2.2.1 Issued #2  
            </td>
        </tr>
    </table>");  
$pdf->AddPage();

$pdf->WriteHTML($stylecss, 1);
$pdf->WriteHTML("<table>
        <thead>
        <tr>
            <td  colspan='3'>
                <a class='navbar-brand'><img  src='./assets/images/STR.jpg' 
                style='height:30px; width:auto;' /></a> 
            </td>
            <td align='center' colspan='11' class='f12'>
                <h2><b>ใบรายงานการชั่งเคมี</b></h2>
            </td>
        </tr>
        <tr>
            <td colspan='14' class='f12' align='left'>
                Chemical : 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                วันที่ : $date
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;           
                เวลา  : $shiftA  น. ถึง $shiftB น.
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Shift : ".strtoupper($mode)."
            </td>
        </tr>
        <tr>
            <td rowspan='2' width='6%'>สูตรเคมี (ยาง) </td>
            <td rowspan='2'>Document <br> No.</td>
            <td rowspan='2'>Issued <br> No.</td>
            <td rowspan='2'>จำนวนที่สั่ง <br> Batch</td>
            <td rowspan='2'>จำนวนที่ได้ <br> Batch</td>
            <td rowspan='2'>ส่วนผสม <br> Ingredients</td>
            <td rowspan='2'>วันที่เคมี <br> Date</td>
            <td rowspan='2'>Spec.น้ำหนัก<br>ต่อ Batch (Kg.)</td>
            <td colspan='3'>น้ำหนักที่ตรวจสอบ/สูตร(Kg.)</td>
            <td colspan='2'>เขียน Code เคมีบนถุง</td>
            <td rowspan='2'>หมายเหตุ <br>Remark</td>
        </tr>
        <tr>
            <td>Act.1 (Kg.)</td>
            <td>Act.2 (Kg.)</td>
            <td>Act.3 (Kg.)</td>
            <td>Pass</td>
            <td>Reject</td>
        </tr>
        </thead>
    ");
        foreach ($datacmc as $v) {
           $pdf->WriteHTML("
            <tr>
            <td>".$v->PastCodeID."</td>
            <td><br></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>".$v->GoodQty."</td>
            <td></td>
            <td></td>
            <td>".$v->ErrorQty."</td>
            <td></td>
            <td></td>
            <td>".$v->GroupName."</td>
            </tr>
            ");
        $i++; } 
$pdf->WriteHTML("</table>");
$pdf->WriteHTML($stylecss_tb2, 1);
$pdf->WriteHTML("<br>
    <table>
        <tr class='border0'>
            <td class='border0'>
                <table>
                    <tr>
                        <td rowspan='2'>พนักงานเคมี</td>
                        <td colspan='3'>จำนวนคน</td>
                    </tr>
                    <tr>
                        <td>มาทำงาน</td>
                        <td>ขาดงาน</td>
                        <td>ออก O.T</td>
                    </tr>
                    <tr>
                        <td>ไทย</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>พม่า</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <td class='border0'>
                <table class='table'>
                    <tr class='right'>
                        <td class='right' align='left'>รายชื่อพนักงานขาดงาน</td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>1.________________________________________________</td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>2.________________________________________________</td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>3.________________________________________________</td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>4.________________________________________________</td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>5.________________________________________________</td>
                    </tr>
                </table>
            </td>
            <td class='border0'>
                <table class='table'>
                    <tr class='right'>
                        <td class='right'>ลงชื่อ________________________หัวหน้ากลุ่ม
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                    <tr class='right'>
                        <td class='right'>ลงชื่อ_______________________หัวหน้าแผนก
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>");
$pdf->WriteHTML("
    <table>
        <tr class='border0'>
            <td class='border0' align='left'>
                Ref.WI-PP-2.1
            </td>
            <td class='border0' align='right'>
                FM-PP-2.1.1 Issued #1  
            </td>
        </tr>
    </table>");    
$pdf->Output(); 
?>