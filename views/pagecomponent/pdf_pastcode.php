<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo root; ?>/assets/css/theme.min.css" />
    <title>PartCode.</title>
    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 18px;
            align-self: center;
        }

        td, tr {
            border: 1px solid #000000;
            padding: 15px;
            text-align: center;
        }

    </style>
</head>

<body>

    <table align="center" cellspacing="0">
        <thead>
        <tr bgcolor="black">
            <td style="color:white;">
                <b>PartCode</b>
            </td>
            <td style="color:white;">
                <b>Item</b>
            </td>
            <td style="color:white;" width="40%">
                <b>ItemName</b>
            </td>
            <td style="color:white;">
                <b>Barcode</b>
            </td>
        </tr>
        </thead>

        <?php foreach ($data as $value) { ?>

        <tr>
            <td>
                <?php echo $value->PastCodeID; ?>
            </td>
            <td>
                <?php echo $value->ItemID; ?>
            </td>
            <td>
                 <?php echo $value->ItemName; ?>
            </td>
            <td align="center" valign="top">
                <?php 
                    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    echo '<img width="200" height="50" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->ItemID, $generator::TYPE_CODE_128)) . '"><br>';
                ?>
            </td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A4');
$mpdf->WriteHTML($html);
$mpdf->Output();