<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Building Report</title>

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
            <td align="center" colspan="4" style="font-size:14px">
                <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>รายงานประวัติการเข้าสู่ระบบ</b>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="f12"><br/>
            <b>วันที่ : <?php echo $date; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>กะ : <?php echo $shift; ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Machine : <?php echo $machine ?></b> 

            
            </td>
        </tr>
        </thead>

        <?php 
        echo "<tr>";
            echo "<td class='f12' width='10%'>รหัสพนักงาน</td>";
            echo "<td class='f12' width='20%'>ชื่อ-นามสกุล</td>";
            echo "<td class='f12' width='10%'>ตำแหน่ง</td>";
            echo "<td class='f12' width='20%'>วันที่และเวลา ที่เข้าสู่ระบบ</td>";
            echo "<td class='f12' width='20%'>วันที่และเวลา ที่ออกจากระบบ</td>";
            echo "<td class='f12' width='10%'>ยอด</td>";
        echo "</tr>";
            foreach ($data as $key => $value) 
            {
                
                echo "<tr>";
                    echo "<td class='f12'>".$value["EmployeeID"]."</td>";
                    echo "<td class='f12'>".$value["Name"]."</td>";
                    echo "<td class='f12'>".$value["BuildType"]."</td>";
                    echo "<td class='f12'>".$value["LoginDate"]."</td>";
                    echo "<td class='f12'>".$value["LogoutDate"]."</td>";
                    echo "<td class='f12'>".$value["Act"]."</td>";
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
$pdf = new mPDF('th','A4-L', 0, '', 5, 5, 5, 20);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>
