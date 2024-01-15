<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ReportQualityChecking".Date("Ymd_His").".xls");
// echo "<pree>";
// print_r($data);
// print_r($_SESSION);
// echo "</pree>";
?>
<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

	<title>Report Quality Checking</title>
	<style>

	</style>
</head>
<body>

	    <table border="1">
	        <thead>
	        <tr>

	            <td align="center" colspan="7" class="f12">
	                <h2>Report Quality Checking</h2>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="4" class="f12" align="left">
	                <b>Date : </b><?php echo date('d-m-Y', strtotime($date)); ?>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <b>Shift : </b><?php if($shift=='day'){echo "กลางวัน";}else{echo "กลางคืน";} ?>
                </td>
              <td colspan="3" class="f12" align="left">
              <p  align="right">  <b align="right"> Export Date :</b><?php  echo date("d-m-Y H:i:s"); ?></p>
              </td>
	        </tr>
	        <tr align="center">
	            <td width="20%";>
	                <b>No.</b>
	            </td>
                <td>
	                <b>Barcode</b>
	            </td>
	            <td>
	                <b>Item No.</b>
	            </td>
	            <td>
	                <b>Size</b>
	            </td>
                <td >
	                <b>CureCode</b>
	            </td>
	            <td>
	                <b>Batch No.</b>
	            </td>
	            <td>
	                <b>CreateDate</b>
	            </td>
	        </tr>
	        </thead>
	        <?php
            	$rows = json_decode($rows);
	        $x = 1;
	        foreach ($rows as $value) {
	        ?>
	        <tr>
                <td width="50" align="center">
                    <?php echo $x ?>
                </td>
                <td width="140" align="left">
                    <?php echo $value->Barcode ?>
                </td>
                <td width="110" align="left">
                    <?php echo $value->ItemID ?>
                </td>
                <td  width="520" align="left">
                    <?php echo $value->NameTH ?>
                </td>
                <td  width="100" align="center">
                    <?php echo $value->CureCode ?>
                </td>
                <td  width="120" align="center">
                    <?php echo $value->Batch ?>
                </td>
                <td  width="180" align="center">
                <?php echo date('d-m-Y H:i:s', strtotime($value->CreateDate)); ?>
                </td>
	        </tr>
	        <?php
             $x++; }
	        ?>
	        
	    </table>

</body>
</html>
<?
$html = ob_get_contents();
ob_end_clean();
