<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Component Report</title>
	<!-- <link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">
	table {
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
    text-align: center;
}

td, tr {
    border: 1px solid #000000;
    text-align: left;
    padding: 6px;
    text-align: center;
}
.f12{
	font-size:12px;
    font-family:"Angsana New";
}

</style>
</head>
<body>

<div class="container">

<table>
    <thead>
    <tr>
        <td  colspan="2">
            <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
            style="height:30px; width:auto;" /></a> 
        </td>
        <td align="center" colspan="4" class="f12">
            <h2><b>Component Report</b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="f12" align="left">
            วันที่ : <?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             
            <?php echo "เวลา  : ".$shiftA." น. ถึง ".$shiftB." น."; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            เครื่อง : -
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
            <?php if ($mode=='a') { ?>
                กะ   : เช้า 
            <?php }else{ ?>
                กะ   : กลางคืน
            <?php } ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            แผนก :  <?php echo $data[0]->SectionName; ?>
            
        </td>
    </tr>
    <tr>
        <td>No.</td>
        <td>PastCodeID</td>
        <td>SCH</td>
        <td>ACT.</td>
        <td>Scrap</td>
        <td>Description</td>
    </tr>
    </thead>

    <?php $i=1;
        foreach ($data as $value) {
            echo "<tr>";
            echo "<td>".$i."</td>";
            echo "<td>".$value->PastCodeID."</td>";
            echo "<td>".$value->SCH."</td>";
            echo "<td>".$value->GoodQty."</td>";
            echo "<td>".$value->ErrorQty."</td>";
            echo "<td>".$value->GroupDescriptiion."</td>";
            echo "</tr>";
        $i++;
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
?>