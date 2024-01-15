<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Rate Deduction Report</title>

<style type="text/css">
    table {
    border-collapse: collapse;
    width: 100%;
    font-size:8px;
    font-family:"Angsana New";
    }

    td, tr, th {
        border: 1px solid #000000;
        text-align: center;
        padding: 4px;
        
    }

    .table {
    border-collapse: collapse;
    width: 100%;
    font-size: 8px;
    }

    .td, .tr, .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 4px;
    }

    .double_td{
    border: 2px solid black;
    }
    .f12{
        font-size:12px;
        font-family:"Angsana New";
    }
    .f10{
        font-size:10px;
        font-family:"Angsana New";
        
    }

</style>
</head>
<body>
<div class="container">
    <table >
        <thead>
        <tr>
            <td colspan="2">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                style="padding-left:10px;height:55px; width:auto;" /></a>
            </td>
            <td align="center" colspan="5" style="font-size:14px">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>รายงานประวัติการหักค่าเรท รายเดือน</b>
            </td>
        </tr>
        <tr>
            <td colspan="7" class="f12"><br/>
            <b>Month : <?php echo $month; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Machine : <?php echo $machine ?></b> 
            </td>
        </tr>
        </thead>

        <?php 
        echo "<tr>";
            echo "<td class='f12' width='15%'>รหัสพนักงาน</td>";
            echo "<td class='f12' width='20%'>ชื่อ-นามสกุล</td>";
            echo "<td class='f12' width='10%'>วัน / เดือน / ปี</td>";
            echo "<td class='f12' width='10%'>Machine</td>";
            echo "<td class='f12' width='10%'>Shift</td>";
            echo "<td class='f12' width='10%'>เงินที่หัก (สาเหตุ)</td>";
            echo "<td class='f12' width='25%'>สาเหตุ</td>";
        echo "</tr>";
            foreach ($data as $key => $value) 
            {
                
                echo "<tr>";
                    echo "<td class='f12' width='10%'>".$value["EmployeeID"]."</td>";
                    echo "<td class='f12' width='20%'>".$value["Name"]."</td>";
                    echo "<td class='f12' width='15%'>".$value["DeductDate"]."</td>";
                    echo "<td class='f12' width='10%'>".$value["Machine"]."</td>";
                    echo "<td class='f12' width='10%'>".$value["Shift"]."</td>";
                    echo "<td class='f12' width='15%'>".$value["Charge"]."</td>";
                    echo "<td class='f12' width='20%'>".$value["Remark"]."</td>";
                echo "</tr>";
			}
		?>
    </table>
    
    
</div>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-P', 0, '', 5, 5, 5, 20);
$pdf->SetDisplayMode('fullpage');
// if ($value['colspan'] == "11")
// {
//     $pdf->SetHtmlFooter('
//         <table>
//             <tr  style="border: 0px;" >
//                 <td style="border: 0px;" colspan="4" class="f12"><br>
               
//                 </td>
//                 <td style="border: 0px;" colspan="7" class="f12"><br>
                
//                 </td>
//             </tr>
//         </table>');
// }
// else
// {
//     $pdf->SetHtmlFooter('
//     <table>
//         <tr  style="border: 0px;" >
//             <td style="border: 0px;" colspan="4" class="f12"><br>
//             <b>ลงชื่อ : ................................................................. (หัวหน้ากลุ่ม)</b>
//             </td>
//             <td style="border: 0px;" colspan="7" class="f12"><br>
//             <b>ลงชื่อ : ................................................................. (หัวหน้าแผนก)</b>
//             </td>
//         </tr>
//     </table>');
// }

$pdf->WriteHTML($html);
$pdf->Output();
?>
