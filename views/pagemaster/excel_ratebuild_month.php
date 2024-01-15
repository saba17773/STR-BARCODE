<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Rate_Build_Monthly_Report".Date("Ymd_His").".xls");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SIAM TRUCK RADIAL TIRE ( <?php echo $machine_type ?> ) </title>

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
    .f10{
        font-size:10px;
        font-family:"Angsana New";
        
    }
    .f8{
        font-size:7px;
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
            <td align="center" colspan="34" style="font-size:14px">
                <b>SIAM TRUCK RADIAL TIRE ( <?php echo $machine_type ?> )</b>
            </td>
        </tr>
        <tr>
            <td colspan="36" class="f12"><br/>
            <b>การจ่ายเงินค่าเลท Tire Building</b>
            <b>Month  : <?php echo $month_now ?></b>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Export Date : <?php echo $export_date ?></b>
            </td>
        </tr>
        <?php
            echo "<tr>";
                echo "<td class='f10' width='8%' rowspan='2'>รหัสพนักงาน</td>";
                echo "<td class='f10' width='17%' rowspan='2'>ชื่อ-นามสกุล</td>";
                // echo "<td class='f10' width='6%' rowspan='2'>รายละเอียด</td>";
                // echo "<td class='f10' width='5%' rowspan='2'>วันเข้างาน</td>";
                // echo "<td class='f10' width='5%' rowspan='2'>อายุงาน (ปี)</td>";
                echo "<td class='f10' width='5%' rowspan='2' text-rotate='90'>ยอดค้าง</td>";
                echo "<td class='f10' width='5%' rowspan='2' text-rotate='90'>ยอดหัก</td>";
                echo "<td class='f10' width='22%' colspan='11'>เดือน ".$month_part."  ".$year_part."</td>";
                echo "<td class='f10' width='40%' colspan='20'>เดือน ".$month_now."  ".$year_now."</td>";
                echo "<td class='f10' width='5%' rowspan='2'>Total</td>";
            echo "</tr>";

            echo "<tr>";
                echo "<td class='f8' width='2%'>21</td>";
                echo "<td class='f8' width='2%'>22</td>";
                echo "<td class='f8' width='2%'>23</td>";
                echo "<td class='f8' width='2%'>24</td>";
                echo "<td class='f8' width='2%'>25</td>";
                echo "<td class='f8' width='2%'>26</td>";
                echo "<td class='f8' width='2%'>27</td>";
                echo "<td class='f8' width='2%'>28</td>";
                echo "<td class='f8' width='2%'>29</td>";
                echo "<td class='f8' width='2%'>30</td>";
                echo "<td class='f8' width='2%'>31</td>";
                echo "<td class='f8' width='2%'>01</td>";
                echo "<td class='f8' width='2%'>02</td>";
                echo "<td class='f8' width='2%'>03</td>";
                echo "<td class='f8' width='2%'>04</td>";
                echo "<td class='f8' width='2%'>05</td>";
                echo "<td class='f8' width='2%'>06</td>";
                echo "<td class='f8' width='2%'>07</td>";
                echo "<td class='f8' width='2%'>08</td>";
                echo "<td class='f8' width='2%'>09</td>";
                echo "<td class='f8' width='2%'>10</td>";
                echo "<td class='f8' width='2%'>11</td>";
                echo "<td class='f8' width='2%'>12</td>";
                echo "<td class='f8' width='2%'>13</td>";
                echo "<td class='f8' width='2%'>14</td>";
                echo "<td class='f8' width='2%'>15</td>";
                echo "<td class='f8' width='2%'>16</td>";
                echo "<td class='f8' width='2%'>17</td>";
                echo "<td class='f8' width='2%'>18</td>";
                echo "<td class='f8' width='2%'>19</td>";
                echo "<td class='f8' width='2%'>20</td>";
            echo "</tr>";

        ?>
        </thead>

        <?php 

            foreach ($data as $key => $value) 
            {
                echo "<tr>";
                    echo "<td class='f10'>".$value["EmployeeID"]."</td>";
                    echo "<td class='f10'>".$value["Name"]."</td>";
                    echo "<td class='f10'> </td>";
                    echo "<td class='f10'>".$value["Charge"]."</td>";
                    echo "<td class='f8'>".$value["D21"]."</td>";
                    echo "<td class='f8'>".$value["D22"]."</td>";
                    echo "<td class='f8'>".$value["D23"]."</td>";
                    echo "<td class='f8'>".$value["D24"]."</td>";
                    echo "<td class='f8'>".$value["D25"]."</td>";
                    echo "<td class='f8'>".$value["D26"]."</td>";
                    echo "<td class='f8'>".$value["D27"]."</td>";
                    echo "<td class='f8'>".$value["D28"]."</td>";
                    echo "<td class='f8'>".$value["D29"]."</td>";
                    echo "<td class='f8'>".$value["D30"]."</td>";
                    echo "<td class='f8'>".$value["D31"]."</td>";
                    echo "<td class='f8'>".$value["D01"]."</td>";
                    echo "<td class='f8'>".$value["D02"]."</td>";
                    echo "<td class='f8'>".$value["D03"]."</td>";
                    echo "<td class='f8'>".$value["D04"]."</td>";
                    echo "<td class='f8'>".$value["D05"]."</td>";
                    echo "<td class='f8'>".$value["D06"]."</td>";
                    echo "<td class='f8'>".$value["D07"]."</td>";
                    echo "<td class='f8'>".$value["D08"]."</td>";
                    echo "<td class='f8'>".$value["D09"]."</td>";
                    echo "<td class='f8'>".$value["D10"]."</td>";
                    echo "<td class='f8'>".$value["D11"]."</td>";
                    echo "<td class='f8'>".$value["D12"]."</td>";
                    echo "<td class='f8'>".$value["D13"]."</td>";
                    echo "<td class='f8'>".$value["D14"]."</td>";
                    echo "<td class='f8'>".$value["D15"]."</td>";
                    echo "<td class='f8'>".$value["D16"]."</td>";
                    echo "<td class='f8'>".$value["D17"]."</td>";
                    echo "<td class='f8'>".$value["D18"]."</td>";
                    echo "<td class='f8'>".$value["D19"]."</td>";
                    echo "<td class='f8'>".$value["D20"]."</td>";
                    echo "<td class='f10'>".$value["Total"]."</td>";
                echo "</tr>";
            }

            echo "<tr>";
                echo "<td class='f10' rowspan ='3' colspan='2'></td>";
                echo "<td class='f10' rowspan ='3'></td>";
                echo "<td class='f10' rowspan ='3'>".$total_charge."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d21."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d22."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d23."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d24."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d25."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d26."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d27."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d28."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d29."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d30."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d31."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d01."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d02."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d03."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d04."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d05."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d06."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d07."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d08."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d09."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d10."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d11."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d12."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d13."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d14."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d15."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d16."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d17."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d18."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d19."</td>";
                echo "<td class='f8' rowspan ='3' text-rotate='90'>".$d20."</td>";

                echo "<td class='f10' rowspan ='3'>".$sum_total."</td>";
            echo "</tr>";
            
		?>
        
    </table >
    
</div>
</body>
</html>