<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
	<title>Report BOM</title>
<!-- 	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">

table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
    font-family: MS Sans Serif;
}

td, tr {
    border: 1px solid #000000
;
    text-align: center;
    padding: 3px;
}
.f12{
	font-size:12px;
}
.f10{
    font-size:12px;
}
.table {
    border-collapse: collapse;
    width: 100%;
    font-size: 8px;
}

.td, .tr, .th {
    border: 0px solid #000000;
    text-align: left;
    padding: 20px;
}
</style>
</head>
<body>

<div class="container">
    <table>
        <thead>
        <tr>
            <td colspan="3" style="padding: 20px;">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
                style="padding-left:10px;height:30px; width:auto;" /></a> 
            </td>
            <td align="center" colspan="4" class="f12">
                <h2>Report BOM</h2>
            </td>
        </tr>
        <tr>
            <td colspan="7" class="f12" align="left">
                <b>Date : </b><?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Shift : </b><?php if($shift=='1'){echo "กลางวัน";}else{echo "กลางคืน";} ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                $all_time = '';
                foreach ($timeset as $v) {
                	// echo $v;
                    $temp_time = explode(' AND ', $v);
                    $all_time .= substr(substr($temp_time[0], 11), 0, 6) . ' -' . substr(substr($temp_time[1], 11), 0, 6) . ', ';
                    // $all_time .= substr(str_replace(' AND ', '-', $v), 10) . '<br>';
                }
                ?>
                <b>Time : </b><?php echo str_replace(' ', '', trim($all_time, ', ')); ?>
            </td>
        </tr>
        <tr>
            <td width="3%">
                <b>No.</b>
            </td>
            <td width="10%">
                <b>Cure Code</b>
            </td>
            <td width="7%">
                <b>Item No.</b>
            </td>
            <td width="10%"> 
                <b>Barcode</b>
            </td>
            <td width="30%">
                <b>Size</b>
            </td>
            <td width="5%">
                <b>Weekly</b>
            </td>
            <td width="5%">
                <b>กลุ่ม</b>
            </td>
        </tr>
        </thead>
        <?php 
        $rows = json_decode($rows);

        $x = 1;
        $qty_total = 0;
        foreach ($rows as $value) {
        ?>
        <tr>
            <td>
                <?php echo $x; ?>
            </td>
            <td>
                <?php echo $value->CuringCode; ?>
                
            </td>
            <td>
                <?php echo $value->ItemID; ?>
            </td>
            <td style="text-align: left;">
                <?php echo $value->Barcode; ?>
                
            </td>
            <td style="text-align: left;">
                <?php echo $value->NameTH; ?>
            </td>
            <td>
                <?php echo $value->Batch; ?>
            </td>
            <td>
                <?php echo $value->Shift; ?>
            </td>
        </tr>
        <?php 
            $x++; //$qty_total+=$value->QTY; 
            }
        ?>
    </table>
</div>

</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 5, 5, 5, 5);  
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output(); 