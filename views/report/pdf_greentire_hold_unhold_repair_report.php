<?php
ob_start();
// var_dump($data);exit;
use App\Components\Utils as U;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Greentire Unhold/Unrepair Report<</title> </head> <body style="font-size: 0.7em;">
            <table width="100%" border="1" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th style="text-align: center;">
                            <img src="./assets/images/str.jpg" width="150" alt="">
                        </th>
                        <th style="text-align: center; padding: 30px;">
                            <div>SIAMTRUCK RADIAL CO. LTD.</div>
                            <div>Greentire Unhold/Unrepair Report</div>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                            <table width="100%" border="0">
                                <tr>
                                    <td colspan="6">
                                        Date : <?php echo $date; ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        BOI : <?php echo $BOIName; ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </thead>
            </table>
            <table width="100%" border="1" cellspacing="0" style="text-align: center;">
                <thead>
                    <tr style="background: #eeeeee;">
                        <th style="padding: 3px;">
                            No.
                        </th>
                        <th style="padding: 3px;">
                            Barcode
                        </th>
                        <th style="padding: 3px;">
                            GT Code
                        </th>
                        <th style="padding: 3px;">
                            Defect
                        </th>
                        <th style="padding: 3px;">
                            Desciption
                        </th>
                        <th style="padding: 3px;">
                            Disposal
                        </th>
                        <th style="padding: 3px;">
                            Authorize By
                        </th>
                        <th style="padding: 3px;">
                            Date/Time
                        </th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (count($result) > 0) : ?>
                        <?php
                        $_rows = $result;
                        $i = 1;
                        $check_deplicate = [];
                        $all_data = [];
                        $_data = [];
                        foreach ($_rows as $value) {
                            if (in_array($value->barcode, $check_deplicate) === false) {
                                $check_deplicate[] = $value->barcode;
                                $_data[] = $value;
                            } else {
                                foreach ($all_data as $z) {
                                    if ($z['barcode'] === $value->Barcode) {
                                        $z['barcode'] = $value->barcode;
                                        $z['code_id'] = $value->code_id;
                                        $z["defect_id"] = $value->defect_id;
                                        $z["defect_desc"] = $value->defect_desc;
                                        $z["disposal"] = $value->disposal;
                                        $z["authorize_by"] = $value->authorize_by;
                                        $z["create_date"] = $value->create_date;
                                        $_data[] = $z;
                                    }
                                }
                            }
                        }
                        ?>
                        <?php $i = 1;
                        foreach ($_data as $row) { ?>
                            <tr>
                                <td style="padding: 3px;"><?php echo $i; ?></td>
                                <td style="padding: 3px;"><?php echo $row->barcode; ?></td>
                                <td style="padding: 3px;"><?php echo $row->code_id; ?></td>
                                <td style="padding: 3px;"><?php echo $row->defect_id; ?></td>
                                <td style="padding: 3px; text-align: left;"><?php echo $row->defect_desc; ?></td>
                                <td style="padding: 3px;"><?php echo $row->disposal; ?></td>
                                <td style="padding: 3px;"><?php echo $row->authorize_by; ?></td>
                                <td style="padding: 3px;"><?php echo $row->create_date; ?></td>
                            </tr>
                        <?php $i++;
                        } ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" style="padding: 10px;">ไม่มีข้อมูล</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 2, 2);
$mpdf->WriteHTML($html);
$mpdf->Output();
