<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=WIP_Final_FG_Report_" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Building master Report </title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>
    <table border="1">
        <tr>
            <td colspan="2">
                <!-- <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
              style="padding-left:10px;height:30px; width:auto;" /></a> -->
            </td>
            <td align="center" colspan="7" class="f12">
                <h2><b>WIP Final FG. Report</b></h2>
            </td>
        </tr>
        <tr>
            <td colspan="9" class="f12">
                <b>Date : <?php echo $date; ?></b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Time : <?php echo $time; ?></b>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                <b>BOI : <?php echo $BOIName; ?></b>
            </td>
        </tr>
        <tr>
            <td>
                No.
            </td>
            <td>
                CureCode
            </td>
            <td>
                Item Name
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
                Return
            </td>
            <td>
                Total
            </td>
        </tr>
        <?php
        $i = 1;
        foreach ($datajson as $value) {
        ?>
            <tr>
                <td>
                    <?php echo $i;
                    $i++; ?>
                </td>
                <td>
                    <?php echo $value->CureCode;
                    ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $value->NameTH; ?>
                </td>
                <td>
                    <?php echo $value->Batch; ?>
                </td>
                <td>
                    <?php if ($value->onhand !== 0) {
                        echo $value->onhand;
                        $total_onhand += $value->onhand;
                    }
                    ?>
                </td>
                <td>
                    <?php if ($value->hold !== 0) {
                        echo $value->hold;
                        $total_hold += $value->hold;
                    }
                    ?>
                </td>
                <td>
                    <?php if ($value->repair !== 0) {
                        echo $value->repair;
                        $total_repair += $value->repair;
                    }
                    ?>
                </td>
                <td>
                    <?php if ($value->return !== 0) {
                        echo $value->return;
                        $total_return += $value->return;
                    }
                    ?>
                </td>
                <td>
                    <?php
                    echo (int)($value->onhand + $value->hold + $value->repair + $value->return);
                    // $rows=array($value->onhand,$value->hold,$value->repair);
                    // $rowsall = array_sum($rows);
                    // if ($rowsall==0) {
                    //     echo "<br>";
                    // }else{
                    //     echo $rowsall;
                    // }
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
                <?php if ($total_onhand !== 0) echo $total_onhand; ?>
            </td>
            <td>
                <?php if ($total_hold !== 0) echo $total_hold; ?>
            </td>
            <td>
                <?php if ($total_repair !== 0) echo $total_repair; ?>
            </td>
            <td>
                <?php if ($total_return !== 0) echo $total_return; ?>
            </td>
            <td>
                <?php
                echo (int)($total_onhand + $total_hold + $total_repair + $total_return);
                // $sumrows=0;
                // foreach ($datajson as $value) {
                // $rows = array($value->onhand,$value->hold,$value->repair);
                // $QQ = array_sum($rows);
                // $sumrows += $QQ;
                // }
                // if ($sumrows==0) {
                //     echo "";
                // }else{
                //     echo $sumrows;
                // }
                ?>
            </td>
        </tr>
    </table>
    <!-- <br>
  <table border="0" width="100%" cellpadding="40">
		<tr>
			<td style="text-align: center; font-weight: bold;">
				________________________________
                <br> Operator
			</td>
			<td style="text-align: center; font-weight: bold;">
			________________________________
                <br> Leader
			</td>
		</tr>
	</table> -->
</body>

</html>