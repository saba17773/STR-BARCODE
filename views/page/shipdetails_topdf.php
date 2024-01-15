<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>WMS Ship Detail Report PDF</title>
    <style>
        body {
            font-size: 10px;
        }
        td {
            height: 25px;
        }
        .page-break {
            page-break-after: always; /* ขึ้นหน้าใหม่ */
        }
        .header{
            /* position: 'fixed'; */
            /* margin-top: 0; */
            /* margin-right: 0; */
            /* bottom: 0; */
            margin-left: 90%;
            /* height: 100px; */
            /* width: 100%; */
        }
    </style>
</head>

<body>

    <?php
        // $pageno = "<div class='header'><strong>Page: {PAGENO}/{nb}</strong></div>";

        $header = "<div class='header'><strong>Page: {PAGENO}/{nb}</strong></div>
        <table border='0' cellspacing='0' width='100%'>
        <tr>
        <td style='text-align: center; padding: 20px;' colspan='4'>
            <img src='./assets/images/logo.png' width='200'>
            <h2><font color='red'>PACKING</font><font color='orange'>DETAILS</font></h2>
        </td>
        </tr>
        <tr>
        <td style='border-top: 0px; padding: 10px; width:15%;'>Ship From :</td>
        <td style='border-top: 0px; padding: 10px; width:40%;'>บริษัท ดีสโตน คอร์ปอเรชั่น จำกัด (มหาชน)</td>
        <td style='border-top: 0px; padding: 10px; width:15%;'>Ship Date :</td>
        <td style='border-top: 0px; padding: 10px; width:30%;'>" . date('d-m-Y', strtotime($data[0]['ACTUALSHIPDATE'])) . "</td>
        </tr>
        <tr>
        <td style='border-top: 0px; padding: 10px;'>สำนักงานใหญ่ :</td>
        <td style='border-top: 0px; padding: 10px;'>84 หมู่ที่ 7 ซอยเพรชเกษม 122 ถนนเพชรเกษม ตำบลออ้มน้อย</td>
        <td style='border-top: 0px; padding: 10px;'>Load ID :</td>
        <td style='border-top: 0px; padding: 10px;'>" . $selectLoadid . "</td>
        </tr>
        <tr>
        <td style='border-top: 0px; padding: 10px;'></td>
        <td style='border-top: 0px; padding: 10px;'>อำเภอกระทุ่มแบน จังหวัดสมุทรสาคร 74130</td>
        <td style='border-top: 0px; padding: 10px;'>Customer Name :</td>
        <td style='border-top: 0px; padding: 10px;'>" . $data[0]['C_COMPANY'] . "</td>
        </tr>              
        </table>";

        echo "<table border='1' cellspacing='0' width='100%'>";
        echo "<tr>";
        echo "<th style='border-top: 0px; padding: 10px; width:5%;'>No.</th>";
        echo "<th style='border-top: 0px; padding: 10px; width:55%;'>Description</th>";
        echo "<th style='border-top: 0px; padding: 10px; width:5%;'>QTY</th>";
        echo "<th style='border-top: 0px; padding: 10px; width:15%;'>BATCH</th>";
        echo "<th style='border-top: 0px; padding: 10px; width:20%;'>SERIALNUMBER</th>";
        echo "<tr>";
        $i=1;
        foreach ($data as $key => $value)
        {                 
            echo "<tr>";
            echo "<td align='center' style='width:5%;'>" . $i . "</td>";
            echo "<td align='left' style='width:55%;'>" . $value["DESCRIPTION"] . "</td>";
            echo "<td align='center' style='width:5%;'> 1 </td>";
            echo "<td align='center' style='width:15%;'>" . $value['LOTTABLE01'] . "</td>";
            echo "<td align='center' style='width:20%;'>" . $value["TemplateSerialNo"] . "</td>";
            echo "</tr>"; 
            $i++;
        }  
        echo "<tr>";
        echo "<td align='center' style='width:5px;' colspan='2'><strong> TOTAL </strong></td>";
        echo "<td align='center' style='width:5px;' colspan='3'><strong> " . count($data) . " </strong></td>";
        echo "</tr>"; 
        echo "</table>"; 
    ?>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 62, 2);
$mpdf->SetHTMLHeader($header);
$mpdf->WriteHTML($html);
$mpdf->Output();
