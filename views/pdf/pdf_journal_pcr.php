<?php 

// echo '<pre>'.print_r($journal['journal_line'], true) . '</pre>';
// exit;

ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
	<title>Journal PCR</title>
<!-- 	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">

table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
    font-family: MS Sans Serif;
}

td, tr {
    border: 1px solid #000000;
    text-align: center;
    padding: 7px;
}
.f12{
	font-size:14px;
}
.f10{
    font-size:10px;
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
            <td colspan="1" width="20%">
                <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
                style="padding-left:10px;height:30px; width:auto;" /></a> 
            </td>
            <td align="center" colspan="3" class="f12">
                <h2>Journal PCR</h2>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: left;">
                <b> Journal : </b><?php echo $journal['journal_info'][0]['JournalID'];?> <br>
                <b> Description : </b><?php echo $journal['journal_info'][0]['JournalDescription'];?> <br>
                <b> Truck : </b><?php echo $journal['journal_info'][0]['TruckID'];?> <br>
                <b> Create Date : </b><?php echo date('Y-m-d H:i', strtotime($journal['journal_info'][0]['CreateDate']));?> <br>
                
                <b> First Scan : </b><?php 

                if ( $journal['first_scan'] === '-') {
                    echo '-';
                } else {
                    echo date('Y-m-d H:i', strtotime($journal['first_scan']));
                }
                
                ?> <br>

                <b> Last Scan : </b><?php 

                if ( $journal['last_scan'] === '-') {
                    echo '-';
                } else {
                    echo date('Y-m-d H:i', strtotime($journal['last_scan']));
                }
                
                ?> <br>
            </td>
        </tr>
        <tr>
            <td width="20%">
                <b>Item No.</b>
            </td>
            <td>
                <b>Item Name</b>
            </td>
            <td width="10%">
                <b>Batch No.</b>
            </td>
            <td width="7%">
                <b>QTY</b>
            </td>
        </tr>
        </thead>
        <?php 
        // $rows = json_decode($rows);
        // $x = 1;
        $qty_total = 0;
        foreach ($journal['journal_line'] as $value) {
        ?>
        <tr>
            <td>
                <?php echo $value['ItemID']; ?>
            </td>
            <td>
                <?php echo $value['ItemName']; ?>
            </td>
            <td>
                <?php echo $value['Batch']; ?>
            </td>
            <td>
                <?php echo $value['QTY']; ?>
            </td>
        </tr>
        <?php  $qty_total += $value['QTY']; } ?>
        <tr>
            <td colspan="2">
                
            </td>
            <td>
                Total
            </td>
            <td>
                <?php echo $qty_total; ?>
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
