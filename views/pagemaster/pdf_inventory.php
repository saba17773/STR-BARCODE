<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Greentire Inventory Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">
	table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
    text-align: center;
}

td, tr {
    border: 1px solid #000000;
    text-align: left;
    padding: 6px;
    text-align: center;
}
.f12{
	font-size:14px;
    font-family:"Angsana New";
}

</style>
</head>
<body>

<div class="container">

<table>
    <thead>
    <tr>
        <td colspan="2">
            <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
            style="padding-left:10px;height:30px; width:auto;" /></a>
        </td>
        <td align="center" colspan="6" class="f12">
            <h2><b>GreenTire Inventory Report</b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="8" class="f12">
            <b>Date : <?php echo $date; ?></b>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<b>Time : <?php echo $time; ?></b>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;

						<b>BOI : <?php echo $BOIName; ?></b>
        </td>
    </tr>
    <tr>
        <td>
            No.
        </td>
				<td>
						Date Build
				</td>
         <td>
            GT Code
        </td>
        <td>
            Batch
        </td>
         <td>
            Onhand
        </td>
         <td>
            Hold
        </td>
         <td>
            Repair
        </td>
         <td>
            Total
        </td>
    </tr>
    </thead>
    <?php
        $total_onhand = 0;
        $total_repair = 0;
        $total_hold = 0;
        $_no = 1;
        foreach ($datajson as $value) {
    ?>
    <tr>
        <td>
            <?php echo $_no; $_no++; ?>
        </td>
				<td>
						<?php echo $value->DateBuild; ?>
				</td>
        <td>
            <?php   echo $value->GT_Code;
            ?>
        </td>
        <td>
            <?php echo $value->Batch; ?>
        </td>
        <td>
            <?php   if ($value->onhand!=0) {
                        echo $value->onhand;
                        $total_onhand+=$value->onhand;
                    }
            ?>
        </td>
        <td>
            <?php   if ($value->hold!=0) {
                        echo $value->hold;
                        $total_hold+=$value->hold;
                    }
            ?>
        </td>
        <td>
            <?php   if ($value->repair!=0) {
                        echo $value->repair;
                        $total_repair+=$value->repair;
                    }
            ?>
        </td>
        <td>
            <?php
                $rows=array($value->onhand,$value->hold,$value->repair);
                $rowsall = array_sum($rows);
                if ($rowsall==0) {
                    echo "<br>";
                }else{
                    echo $rowsall;
                }
            ?>
        </td>
    </tr>
    <?php
        }
    ?>
    <tr>
        <td colspan="4">
            <b>Total</b>
        </td>
        <td>
            <?php echo number_format((int)$total_onhand); ?>
        </td>
        <td>
            <?php echo number_format((int)$total_hold) ; ?>
        </td>
        <td>
            <?php echo number_format((int)$total_repair)  ; ?>
        </td>
        <td>
            <?php
                $sumrows=0;
                foreach ($datajson as $value) {
                $rows = array($value->onhand,$value->hold,$value->repair);
                $QQ = array_sum($rows);
                $sumrows += $QQ;
                }
                if ($sumrows==0) {
                    echo "";
                }else{
                    echo number_format((int)$sumrows);
                }
            ?>
        </td>
    </tr>
</table>

</div>

</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 10, 10, 10, 10);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
?>
