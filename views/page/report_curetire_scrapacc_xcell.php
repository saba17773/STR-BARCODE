<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=Greentire_ScrapACC_AX_Report_" . Date("Ymd_His") . ".xls");
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

    <title>report_curetire_scrapAcc_xcell </title>
    <style>
        body {
            font-size: 0.8em;
        }
    </style>
</head>

<body>

    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <td colspan="2" align="center">

                </td>
                <td align="center" colspan="11" class="f14">
                    <b>SIAMTRUCK RADIAL CO.LTD.</b> <br> <b>GREENTIRE SCRAP FOR ACCOUNT</b>
                </td>
            </tr>
            <tr>
                <td align="left" colspan="13" class="f10">
                    <br>Scrap Date : <?php echo $date; ?>

                </td>
            </tr>

            <tr>
                <th style="border-top: 0px; padding: 10px;">ลำดับ</th>
                <th style="border-top: 0px; padding: 10px;">GT Code</th>
                <th style="border-top: 0px; padding: 10px;">MC</th>
                <th style="border-top: 0px; padding: 10px;">Date Build</th>
                <th style="border-top: 0px; padding: 10px;">Shift Build</th>
                <th style="border-top: 0px; padding: 10px;">Item No.</th>
                <th style="border-top: 0px; padding: 10px;">Barcode</th>
                <th style="border-top: 0px; padding: 10px;">Date Hold</th>
                <th style="border-top: 0px; padding: 10px;">Defect Description</th>
                <th style="border-top: 0px; padding: 10px;">Date/Time</th>
                <th style="border-top: 0px; padding: 10px;">Defect Description</th>
                <th style="border-top: 0px; padding: 10px;">Weekly</th>
                <th style="border-top: 0px; padding: 10px;">Shift Scrap</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $check_deplicate = [];
            $all_data = [];
            $data_ = [];
            foreach ($data as $value) {
                $inr = in_array($value["Barcode"], $check_deplicate);
                if ($inr === false) {
                    $check_deplicate[] = trim($value['Barcode']);
                    $data_[] = $value;
                    // echo $i . var_dump($inr) . "<br>";
                    // echo $i . " " . trim($value['Barcode']) . "<br>";
                    // $i++;
                } else {
                    foreach ($all_data as $z) {
                        if (trim($z['Barcode']) === trim($value['Barcode'])) {

                            $z['Barcode'] = $value['Barcode'];
                            $z['GT_Code'] = $value['GT_Code'];
                            $z["IDItem"] = $value["IDItem"];
                            $z["DefectID"] = $value["DefectID"];
                            $z["DefectDesc"] = $value["DefectDesc"];
                            $z["Batch"] = $value["Batch"];
                            $z["Shift"] = $value["Shift"];
                            $z["CreateDate"] = $value["CreateDate"];
                            $z['MC'] = $value['MC'];
                            $z['Shift_Build'] = $value['Shift_Build'];
                            $data_[] = $z;
                        }
                    }
                }
            }

            // echo "<pre>" . print_r($data_, true). '</pre>'; exit;
            ?>

            <?php foreach ($data_ as $value) { ?>
                <tr>
                    <td style="padding: 5px; text-align: center;"><?php echo $i; ?></td>

                    <td style="padding: 5px;"><?php echo $value["GT_Code"]; ?></td>
                    <td style="padding: 5px;"><?php echo $value["MC"]; ?></td>
                    <td style="padding: 5px;"><?php if ($value["DateBuild"] === null || $value["DateBuild"] === "") {
                                                    echo "-";
                                                } else {
                                                    $createdate = date('d-m-Y H:i', strtotime($value["DateBuild"]));
                                                    echo $createdate;
                                                } ?></td>
                    <td style="padding: 5px; text-align: center;"><?php echo $value["Shift_Build"]; ?></td>
                    <td style="padding: 5px;">
                        <?php
                        echo $value["IDItem"];
                        // if ($value["IDItem"] === null || $value["IDItem"] === "") {
                        // 	echo "-";
                        // } else {
                        // 	echo $value["IDItem"];
                        // }
                        ?>
                    </td>
                    <td style="padding: 5px;"><?php echo $value["Barcode"]; ?></td>
                    <td style="padding: 5px;"><?php
                                                if ($value["CreateDateHold"] === null || $value["CreateDateHold"] === "") {
                                                    echo "-";
                                                } else {
                                                    $createdatehold = date('d-m-Y H:i', strtotime($value["CreateDateHold"]));
                                                    echo $createdatehold;
                                                } ?></td>
                    <td style="padding: 5px;"><?php echo $value["DefectDescHold"]; ?></td>
                    <td style="padding: 5px;">
                        <?php
                        if ($value["CreateDate"] === null || $value["CreateDate"] === "") {
                            echo "-";
                        } else {
                            $createdate = date('d-m-Y H:i', strtotime($value["CreateDate"]));
                            echo $createdate;
                        }
                        ?>
                    </td>
                    <td style="padding: 5px; text-align: left;"><?php echo $value["DefectDesc"]; ?></td>
                    <td style="padding: 5px; text-align: center;"><?php echo $value["Batch"]; ?></td>
                    <td style="padding: 5px; text-align: center;"><?php echo $value["Shift"]; ?></td>
                </tr>
            <?php $i++;
            } ?>
        </tbody>
    </table>
    <table border="0" width="100%" cellpadding="30">
        <tr>
            <td>
                ผู้ส่ง ___________________________ (Q-Tech)
            </td>
            <td>
                ผู้รับ ___________________________ (ERP-AX)
            </td>
        </tr>
    </table>
</body>

</html>
<?
$html = ob_get_contents();
ob_end_clean();
