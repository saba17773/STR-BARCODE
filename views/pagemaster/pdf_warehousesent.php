<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-874"> -->
    <title>รายงานส่งยางเข้าคลังสินค้า</title>
    <!-- 	<link rel="stylesheet" href="<?php echo APP_ROOT; ?>/assets/css/theme.min.css" /> -->
    <style type="text/css">
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 10px;
        font-family: MS Sans Serif;
    }

    td,
    tr {
        border: 1px solid #000000;
        text-align: center;
        padding: 7px;
    }

    .f12 {
        font-size: 14px;
    }

    .f10 {
        font-size: 10px;
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        font-size: 8px;
    }

    .td,
    .tr,
    .th {
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
                    <td colspan="2">
                        <a class="navbar-brand"><img src="./assets/images/STR.jpg"
                                style="padding-left:10px;height:30px; width:auto;" /></a>
                    </td>
                    <td align="center" colspan="5" class="f12">
                        <h2>รายงานส่งยางเข้าคลังสินค้า</h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="f12" align="left">
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
                </tr>
                <tr>
                    <td width="5%">
                        <b>No.</b>
                    </td>
                    <td>
                        <b>Item No.</b>
                    </td>
                    <td>
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
                    <td width="15%" class="fff">
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
        <table class="table">
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