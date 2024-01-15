<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Actual Building Report</title>

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
                <td colspan="3">
                    <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
                    style="padding-left:10px;height:55px; width:auto;" /></a>
                </td>
                <td align="center" colspan="7" style="font-size:14px">
                    <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>Actual Building Report</b>
                </td>
            </tr>
            <tr>
                <td colspan="10" class="f12"><br/>
                <b>DATE : <?php echo $date; ?></b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>SHIFT : <?php echo $shift ?></b> 
                </td>
            </tr>
            <tr>
                <td rowspan="1" class="f10" width="5%"><b>เครื่อง</b></td>
                <td rowspan="1" class="f10" width="7%"><b>รหัส</b></td>
                <td rowspan="1" class="f10" width="15%"><b>ชื่อ - สกุล</b></td>
                <td rowspan="1" class="f10" width="6%"><b>ตำแหน่ง</b></td>
                <td rowspan="1" class="f10" width="15%"><b>วันที่และเวลา เข้าสู่ระบบ</b></td>
                <td rowspan="1" class="f10" width="15%"><b>วันที่และเวลา ออกจากระบบ</b></td>
                <td rowspan="1" class="f10" width="5%"><b>SCH.</b></td>
                <td rowspan="1" class="f10" width="6%"><b>Act.</b></td>
                <td rowspan="1" class="f10" width="5%"><b>ยางเสีย</b></td>
                <td rowspan="1" class="f10" width="6%"><b>รวม</b></td>
            </tr>
        </thead>
        <tr>
        
        </tr>
        <?php 
			$machine = "";
            foreach ($data as $key => $value) 
            {
			    // echo $value['Boiler'].$value['ItemID'];
                // echo "<br>";
				echo "<tr>";
                if ($machine != $value["Machine"]) 
                {
                    
					echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["Machine"]."</td>";
                }
                    echo "<td class='f12' height='22px' >".$value["EmployeeID"]."</td>";
                    echo "<td class='f12'>".$value["Name"]."</td>";
                    echo "<td class='f12'>".$value["BuildType"]."</td>";
                    echo "<td class='f12'>".$value["LoginDate"]."</td>";
                    echo "<td class='f12'>".$value["LogoutDate"]."</td>";
                    if ($machine != $value["Machine"]) 
                    {
                        
                        echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["SCH"]."</td>";
                    }
                    // echo "<td class='f12'>".$value["SCH"]."</td>";
                    echo "<td class='f12'>".$value["Act"]."</td>";
                    if ($machine != $value["Machine"]) 
                    {
                        
                        echo "<td class='f12' rowspan=".$value['rowspan'].">".$value["SCARP_MAC"]."</td>";
                    }
                    // echo "<td class='f12'>".$value["SCARP_MAC"]."</td>";
                    echo "<td class='f12'>".$value["TOTAL"]."</td>";
                    
				echo "</tr>";
				$machine = $value["Machine"];
			}
		?>        
    </table >
    
</div>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 5, 5, 5, 5);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>
