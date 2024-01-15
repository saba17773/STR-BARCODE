<?php
 header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=warehouse".Date("Ymd_His").".xls");
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

	<title>รายงานส่งยางเข้าคลังสินค้า</title>
	<style>
		/* body {
			font-size: 0.8em;
		} */
	</style>
</head>
<body>

	    <table border="1" width=88%>
	        <thead>
	        <tr>

	            <td align="center" colspan="7" class="f12">
	                <h2>รายงานส่งยางเข้าคลังสินค้า</h2>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="4" class="f12" align="left">
	                <b>Date : </b><?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <b>Shift : </b><?php if($shift=='day'){echo "กลางวัน";}else{echo "กลางคืน";} ?>
	                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	                <?php
	                $all_time = '';
	                foreach ($timeset as $v) {
	                    preg_match_all('(..:..)', $v, $_t);
	                    foreach ($_t as $x) {
	                        $all_time .= $x[0] . '-' . $x[1] . ', ';
	                    }
	                }
	                ?>
	                <b>Time : </b><?php echo trim($all_time, ', '); ?>
                  &nbsp;&nbsp;&nbsp;
                 <b>BOI : </b><?php echo $BOIName; ?>


	            </td>
              <td colspan="3" class="f12" align="left">
              <p  align="right">  <b align="right"> Export Date :</b><?php  echo date("Y-m-d H:i:s"); ?></p>
              </td>
	        </tr>
	        <tr>
	            <td width="20%";>
	                <b>No.</b>
	            </td>
	            <td>
	                <b>Item No.</b>
	            </td>
	            <td >
	                <b>Cure Code</b>
	            </td>
	            <td>
	                <b>Item Name</b>
	            </td>
	            <td>
	                <b>Batch No.</b>
	            </td>
	            <td>
	                <b>QTY</b>
	            </td>
	            <td>
	                <b>Remark</b>
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
	                <?php echo $value->ItemID; ?>
	            </td>
	            <td>
	                <?php echo $value->CuringCode; ?>
	            </td>
	            <td style="text-align: left;">
	                <?php echo $value->NameTH; ?>
	            </td>
	            <td>
	                <?php echo $value->Batch; ?>
	            </td>
	            <td>
	                <?php echo $value->QTY; ?>
	            </td>
	            <td>

	            </td>
	        </tr>
	        <?php
	            $x++; $qty_total+=$value->QTY; }
	        ?>
	        <tr>
	            <td colspan="5">
	                <b>Total</b>
	            </td>
	            <td>
	                <?php
	                    // $sum = 0;
	                    // $sumrows=0;
	                    // foreach ($datajson as $value) {
	                    // $rows = array($value->QTY);
	                    // $QQ = array_sum($rows);
	                    // $sumrows += $QQ;
	                    // }
	                    // if ($sumrows==0) {
	                    //     echo "";
	                    // }else{
	                    //     echo $sumrows;
	                    // }
	                     echo number_format($qty_total);
	                ?>
	            </td>
	            <td>

	            </td>
	        </tr>
	    </table>
	    <!-- <table class="table">
	        <tr class="tr">
	            <td class="td f10" align="center">
	                ผู้ส่ง ___________________________(Final Finishing)
	            </td>
	            <td class="td f10" align="center">
	                 ผู้รับ ___________________________(คลังสินค้า)
	            </td>
	        </tr>
	        <tr class="tr">
	            <td class="td f10" align="center">
	               ผู้ตรวจ _________________________(Final Finishing)
	            </td>
	            <td class="td f10" align="center">
	                ผู้ตรวจ _________________________(คลังสินค้า)
	            </td>
	        </tr>
	    </table> -->

</body>
</html>
<?
$html = ob_get_contents();
ob_end_clean();
