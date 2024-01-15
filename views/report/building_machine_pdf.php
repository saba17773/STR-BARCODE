<?php ob_start();

$pdf=new mPDF('th','A4', 0, '', 3, 3, 48.5, 5);
$pdf->AliasNbPages();

function txtHeader($date,$shift){
    $txtheader = "<table cellpadding='10' style='border: 1px solid #000000; border-collapse: collapse;' width='100%'>
        <tr>
            <th colspan='4' align='center' style='border: 1px solid #000000;border-collapse: collapse;'>
                <img src=./assets/images/STR.jpg style='height:50px; width:auto;' />
            </th>
            <th colspan='4' valign='middle' style='border: 1px solid #000000;border-collapse: collapse;'>
                <h2>Building Report By Machine</h2>
            </th>
        </tr>
        <tr>
            <td colspan='8' align='left' style='font-size:15px; border: 1px solid #000000;border-collapse: collapse;'>
                <b>Date : </b> ".$date." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Shift : </b>".$shift."
            </td>
        </tr>
        <tr>
            <td width='5%' align='center' style='border: 1px solid #000000;font-size:12px;' align='center'>
                No
            </td>
            <td width='7%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                Mc
            </td>
            <td width='12%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                GT.Code
            </td>
            <td width='13%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'> 
                Barcode No
            </td>
            <td width='22%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                Barcode No
            </td>
            <td width='19%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                Building Date
            </td>
            <td width='15%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                Disposition
            </td>
            <td width='7%' align='center' style='font-size:12px; border: 1px solid #000000;border-collapse: collapse;'>
                Shift
            </td>
        </tr></table>";
    return $txtheader;
}

$dataheader = txtHeader($date,$shift);
$pdf->SetHTMLHeader($dataheader);
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$x = 1;
foreach ($data as $value) {

    $m = $value->BuildingNo;  

    if ($m!=$j) {
        $pdf->AddPage();
        $x=1;
    }       
        $newdate = date("d-m-Y H:i:s", strtotime($value->CreateDate));
        // $pdf->WriteHTML($value->Description);
         $pdf->WriteHTML("<table style='margin-top: 0px; border: 1px solid #000000;border-collapse: collapse;font-size:12px;' cellpadding='10' width='100%'>
                <tr style='border: 1px solid #000000;'>
                    <td width='5%'  style='border: 1px solid #000000;' align='center' >".$x."</td>
                    <td width='7%' style='border: 1px solid #000000;' align='center' >".$value->BuildingNo."</td>
                    <td width='12%' style='border: 1px solid #000000;' align='center' >".$value->GT_Code."</td>
                    <td width='13%' style='border: 1px solid #000000;' align='center' >".$value->Barcode."</td>
                    <td width='22%' style='border: 1px solid #000000;' align='center' >
                      <img width='140' height='20' src='data:image/png;base64,". base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . "'>
                    </td>
                    <td width='19%' style='border: 1px solid #000000;' align='center' >".$newdate."</td>
                    <td width='15%' style='border: 1px solid #000000;' align='center' >".$value->DisposalDesc."</td>
                    <td width='7%'  style='border: 1px solid #000000;' align='center' >".$value->Description."</td>
                </tr></table>
        ");

        $j = $value->BuildingNo;  

    $x++;
    }
$pdf->Output();

