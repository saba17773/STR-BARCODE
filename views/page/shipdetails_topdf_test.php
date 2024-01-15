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
        $pageno = "<div class='header'><strong>Page: {PAGENO}/{nb}</strong></div>";
        $currentSO = null;

        foreach ($data as $key => $value)
        {
            if ($currentSO !== $value['EXTERNORDERKEY'])
            {
                if ($currentSO !== null)
                {                   
                    echo "</table>";
                    echo "<div class='page-break'></div>";
                }
                
                echo "<table border='0' cellspacing='0' width='100%'>";
                echo "<tr>";
                echo "<td style='text-align: center; padding: 20px;' colspan='4'>
                    <img src='./assets/images/logo.png' width='200'>
                    <h2><font color='red'>PACKING</font><font color='orange'>DETAILS</font></h2>
                </td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border-top: 0px; padding: 10px; width:15%;'>Ship From :</td>";
                echo "<td style='border-top: 0px; padding: 10px; width:40%;'>บริษัท ดีสโตน คอร์ปอเรชั่น จำกัด (มหาชน)</td>";
                echo "<td style='border-top: 0px; padding: 10px; width:15%;'>Ship Date :</td>";
                echo "<td style='border-top: 0px; padding: 10px; width:30%;'>" . date('d-m-Y', strtotime($value['ACTUALSHIPDATE'])) . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border-top: 0px; padding: 10px;'>สำนักงานใหญ่ :</td>";
                echo "<td style='border-top: 0px; padding: 10px;'>84 หมู่ที่ 7 ซอยเพรชเกษม 122 ถนนเพชรเกษม ตำบลออ้มน้อย</td>";
                echo "<td style='border-top: 0px; padding: 10px;'>Sale Order :</td>";
                echo "<td style='border-top: 0px; padding: 10px;'>" . $value['EXTERNORDERKEY'] . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td style='border-top: 0px; padding: 10px;'></td>";
                echo "<td style='border-top: 0px; padding: 10px;'>อำเภอกระทุ่มแบน จังหวัดสมุทรสาคร 74130</td>";
                echo "<td style='border-top: 0px; padding: 10px;'>Customer Name :</td>";
                echo "<td style='border-top: 0px; padding: 10px;'>" . $value['C_COMPANY'] . "</td>";
                echo "</tr>";              
                echo "</table>";

                // echo "<br><br><br><br><br><br><br><br><br><br>";
                echo "<table border='1' cellspacing='0' width='100%'>";
                echo "<tr>";
                echo "<th style='border-top: 0px; padding: 10px; width:5%;'>No.</th>";
                echo "<th style='border-top: 0px; padding: 10px; width:55%;'>Description</th>";
                echo "<th style='border-top: 0px; padding: 10px; width:5%;'>QTY</th>";
                echo "<th style='border-top: 0px; padding: 10px; width:15%;'>BATCH</th>";
                echo "<th style='border-top: 0px; padding: 10px; width:20%;'>SERIALNUMBER</th>";
                echo "<tr>";
                $currentSO = $value['EXTERNORDERKEY'];
                $i=1;
            }
            echo "<tr>";
            echo "<td align='center' style='width:5px;'>" . $i . "</td>";
            echo "<td align='left' style='width:5px;'>" . $value["DESCRIPTION"] . "</td>";
            echo "<td align='center' style='width:5px;'> 1 </td>";
            echo "<td align='center' style='width:5px;'>" . $value['LOTTABLE01'] . "</td>";
            echo "<td align='center' style='width:5px;'>" . $value["TemplateSerialNo"] . "</td>";
            echo "</tr>"; 
            $i++;
        }
        echo "</table>";   
    ?>

</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF('th', 'A4', 0, '', 2, 2, 15, 2);
$mpdf->SetHTMLHeader($pageno);
$mpdf->WriteHTML($html);
$mpdf->Output();
