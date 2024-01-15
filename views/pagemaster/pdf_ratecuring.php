<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Curing Rate Report</title>

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

    .f12{
        font-size:12px;
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
            <td align="center" colspan="7" style="font-size:14px">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Curing Rate Report</b>
            </td>
        </tr>
        <tr>
            <td colspan="9" class="f12"><br/>
            <b>DATE : <?php echo $date; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>SHIFT : <?php echo $shift; ?></b> 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Export Date : <?php echo $export_date; ?></b> 
            </td>
        </tr>
        <tr>
            <td rowspan='1' class='f12' width='10%'>รหัสพนักงาน</td>
            <td rowspan='1' class='f12' width='20%'>ชื่อ - นามสกุล</td>
            <td rowspan='1' class='f12' width='9%'>Press</td>
            <td rowspan='1' class='f12' width='9%'>Size</td>
            <td rowspan='1' class='f12' width='15%'>Code</td>
            <td rowspan='1' class='f12' width='10%'>TopTurn</td>
            <td rowspan='1' class='f12' width='9%'>Actual</td>
            <td rowspan='1' class='f12' width='9%'>Rate pay</td>
            <td rowspan='1' class='f12' width='9%'>Total Rate</td>
        </tr>
        </thead>
        
        <?php 
            $CreateBy = "";
            $PressNo = "";
            foreach ($data as $key => $value) 
            {

                echo "<tr>";
                if ($CreateBy != $value["CreateBy"]) 
                {
                        
                    echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["EmployeeID"]."</td>";
                    echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["Name"]."</td>";
                }
                if($CreateBy != $value["CreateBy"] || $PressNo != $value["PressNo"])
                {
                    echo "<td class='f12' rowspan=".$value['rowspan_arm']." >".$value["PressNo"]."</td>";
                }

                    // echo "<td class='f12' >".$value["PressNo"]."</td>";
                    echo "<td class='f12' height='23px' >".$value["PressSide"]."</td>";
                    echo "<td class='f12' >".$value["CuringCode"]."</td>";
                    echo "<td class='f12' >".$value["TopTurn"]."</td>";
                    echo "<td class='f12' >".$value["Act"]."</td>";

                if($CreateBy != $value["CreateBy"] || $PressNo != $value["PressNo"])
                {
                    echo "<td class='f12' rowspan=".$value['rowspan_arm']." >".$value["RatePay"]."</td>";
                }
               
                if ($CreateBy != $value["CreateBy"]) 
                {
                    
                    echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["TOTAL"]."</td>";
                }       

                echo "</tr>";
                $PressNo = $value["PressNo"];
                $CreateBy = $value["CreateBy"];
                
			}
		?>
        
    </table >
    
</div>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-P', 0, '', 5, 5, 5, 25);
$pdf->SetDisplayMode('fullpage');
$pdf->SetHtmlFooter('
    <table>
        <tr  style="border: 0px;" >
            <td style="border: 0px;" colspan="2" class="f12"><br>
            <b>ลงชื่อ : ................................................................. (หัวหน้ากลุ่ม)</b>
            </td>
            <td style="border: 0px;" colspan="7" class="f12"><br>
            <b>ลงชื่อ : ................................................................. (หัวหน้าแผนก)</b>
            </td>
        </tr>
    </table>'
);
$pdf->WriteHTML($html);
$pdf->Output();
?>
