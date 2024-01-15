<?php ob_start();
use App\Components\Database;
use Wattanar\Sqlsrv;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CuringPress Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->

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
        padding: 5.5px;
    }

    .table {
    border-collapse: collapse;
    width: 100%;
    font-size: 8px;
    }

    .td, .tr, .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 8px;
    }

    .double_td{
    border: 2px solid black;
    }
    .f12{
        font-size:14px;
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
    <table>
        <tr>
            <td colspan="2">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
                style="padding-left:10px;height:30px; width:auto;" /></a> 
            </td>
            <td align="center" colspan="9" class="f12">
                <h2><b>ใบรายงานจำนวนยางที่อบ</b></h2>
            </td>
        </tr>
        <tr>
            <td colspan="9" class="f10 td" align="left">
            <b>Date : </b><?php echo $datecuring; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Press : </b>
            <?php   echo $press.'&nbsp;&nbsp;'.'L&nbsp;/&nbsp;'; 
                    $elements = array();
                    foreach($datajsonCL as $value) {
                        $elements[] = $value->CuringCode;
                    }
                    echo implode(',', $elements);
            ?>  
            &nbsp;&nbsp;
            <b>Press : </b>
            <?php   echo $press.'&nbsp;&nbsp;'.'R&nbsp;/&nbsp;'; 
                    $elements = array();
                    foreach($datajsonCR as $value) {
                        $elements[] = $value->CuringCode;
                    }
                    echo implode(',', $elements);
            ?>
            <br>
            <b>GT.Code(L) : </b>
            <?php   $elements = array();
            $gtl = '';
            foreach($datajsonGTL as $value) {
                $gtl .= $value->GT_Code;
            }
            // echo implode(',', $elements);
            echo $gtl;
            ?>  
            &nbsp;
            <b>/GT.Code(R) : </b>
            <?php   $elements = array();
            foreach($datajsonGTR as $value) {
                $elements[] = $value->GT_Code;
            }
            echo implode(',', $elements);
            ?>   
            &nbsp;&nbsp;&nbsp;
            <b>Weekly : </b>
            <?php   $elements = array();
            foreach($datajsonW as $value) {
                $elements[] = $value->Batch;
            }
            echo implode(',', $elements);
            ?>   
            </td>
            <td colspan="2" class="f10 td" align="left"><br>
            <b>กลุ่ม : </b>
            &nbsp;&nbsp;&nbsp;
            <b>กะที่ : 
            <?php if ($shift=='day') {
                echo 'กลางวัน';
            }else{
                echo 'กลางคืน';
            } ?></b><br>
            <b>ผู้บันทึก : </b>
            </td>

        </tr>
        <tr>
            <th rowspan="2" width="10%"><i><u>เวลาที่อบ</u></i></th>
            <th rowspan="2" width="5%">ลำดับ</th>
            <th colspan="3">L</th>
            <th rowspan="2" width="10%"><i><u>เวลาที่อบ</u></i></th>
            <th rowspan="2" width="5%"><i><u>ลำดับ</u></i></th>
            <th colspan="3">R</th>
            <th rowspan="2" width="10%"><i><u>หมายเหตุ</u></i></th>
        </tr>
        <tr>
            <td>Serial L</td>
            <td><i><u>ID BarCode</u></i></td>
            <td width="5%"><i><u>Build Shift</u></i></td>
            <td>Serial R</td>
            <td><i><u>ID BarCode</u></i></td>
            <td width="5%"><i><u>Build Shift</u></i></td>
        </tr>

        <?php 
            $count_r = 0;
            $count_l = 0;
            foreach ($datajson as $value) {     

                if (isset($value->S1_Row)) {
                    $count_l++;
                }

                 if (isset($value->S2_Row)) {
                    $count_r++;
                }
        ?>
        <tr>
            <td>
                <?php 
                    // echo $value->S1_CuringTime; 
                   echo substr($value->S1_CuringTime, 0,5); 
                ?>
            </td>
            <td>
                <?php echo $value->S1_Row; ?>
            </td>
            <td>
                <?php echo $value->S1_TemplateSerialNo; ?>
            </td>
            <td>
                <?php echo $value->S1_Barcode; ?>
            </td>
            <td>
                <?php echo $value->S1_Description; ?>
            </td>
            <td>
                <?php 
                    // echo $value->S1_CuringTime;
                    echo substr($value->S2_CuringTime, 0,5); 
                ?>
            </td>
            <td>
                <?php echo $value->S2_Row; ?>
            </td>
            <td>
                <?php echo $value->S2_TemplateSerialNo; ?>
            </td>
            <td>
                <?php echo $value->S2_Barcode; ?>
            </td>
            <td>
                <?php echo $value->S2_Description; ?>
            </td>
            <td>
                <?php 
                if ($value->S1_CuringCode !== $value->S2_CuringCode) {
                     if ($chk!=$value->S1_CuringCode) {
                        echo $value->S1_Row.$value->S1_PressSide.$value->S1_CuringCode."&nbsp;";
                    }
                    if ($chk!=$value->S2_CuringCode) {
                        echo $value->S2_Row.$value->S2_PressSide.$value->S2_CuringCode;
                    }
                } else {
                    echo "";
                }
               
                ?>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <td colspan="2">
                
            </td>
            <td colspan="3" align="left">
                <i><u>รวม L : </u></i>
            <?php $conn = Database::connect();
            $datecur = date('Y-m-d', strtotime($datecuring));
            $datenight = str_replace('-', '/', $datecuring);
            $datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
            $date1 = $datecur.' 08:00:01';   $date2 = $datecur.' 20:00:00';
            $date3 = $datecuringnight.' 20:00:01';  $date4 = $datecuringnight.' 08:00:00';
            $sql = "SELECT CONVERT(time,I.CuringDate)CuringTime
                ,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
                ,I.TemplateSerialNo
                ,I.Barcode
                ,S.Description
                ,I.PressNo
        FROM InventTable I
        LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
        LEFT JOIN ShiftMaster S ON T.Shift=S.ID
        WHERE I.CuringDate BETWEEN '$date1' AND '$date2' AND I.PressSide='L' AND I.PressNo='$press' 
            GROUP BY
                CONVERT(time,I.CuringDate)
                ,I.TemplateSerialNo
                ,I.Barcode
                ,S.Description  
                ,I.PressNo";
            $rs = sqlsrv_query($conn,$sql);
            $ids = array(); 
            while ($row = sqlsrv_fetch_array($rs))  
            {
                $countl = $row["Row"]; 
            } 
            if (isset($countl)) {
                // echo $countl." เส้น";
            }

            echo $count_l;
            
            ?>
            </td>
            <td colspan="2">
                
            </td>
            <td colspan="3" align="left">
                <i><u>รวม R :</u></i> 
            <?php $conn = Database::connect();
            $datecur = date('Y-m-d', strtotime($datecuring));
            $sql = "SELECT CONVERT(time,I.CuringDate)CuringTime
                ,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
                ,I.TemplateSerialNo
                ,I.Barcode
                ,S.Description
                ,I.PressNo
        FROM InventTable I
        LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
        LEFT JOIN ShiftMaster S ON T.Shift=S.ID
        WHERE CONVERT(date,I.CuringDate)='$datecur' AND I.PressSide='R' AND I.PressNo='$press' 
            GROUP BY
                CONVERT(time,I.CuringDate)
                ,I.TemplateSerialNo
                ,I.Barcode
                ,S.Description  
                ,I.PressNo";
            $rs = sqlsrv_query($conn,$sql);
            $ids = array(); 
            while ($row = sqlsrv_fetch_array($rs))  
            {
                $countr = $row["Row"]; 
            } 
            if (isset($countr)) {
                // echo $countr." เส้น";
            }
            echo $count_r;
            ?>
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
        <td colspan="11">
            <b><i><u>รวมยอดทั้งหมด
            <?php 
            
            if (isset($countl) && isset($countr)) {
                $sum=($countl+$countr); 
                // echo $sum;
            } echo (int)($count_l+$count_r);
            ?> เส้น</i>u></i> </b>
        </td>
    </tr>
    </table>

</div>

</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);  
$pdf->SetDisplayMode('fullpage');
$pdf->SetHTMLFooter('
<table class="table">
<tr class="tr">
    <td class="td" align="left">
        Ref.WI-PP-2.12
    </td>
    <td class="td" align="right">
        FM-PP-2.12.1,Issued #2
    </td>
</tr>
</table>
');
$pdf->WriteHTML($html);
$pdf->Output(); 
?>