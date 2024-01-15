<?php ob_start();
$datajson = json_decode($datajson);

$a = count($datajson) - (  floor(count($datajson) / 26 ) * 26 );

	$stylesheet = " table{
	                  width: 100%;
	                }
	                tr{
	                  border: 0px;
	                }
	                td{
	               	  border: 0px;
	               	  text-align: center;
	               	  padding: 15px;
	                }";
	// $footer = "
	//         <table class='table' width='100%'>
	// 					<tr class='tr'>
	// 					    <td class='td' align='left'>
	// 					        Ref.WI-MP-1.9
	// 					    </td>
	// 					    <td class='td' align='right'>
	// 					        " . $issue ."
	// 					    </td>
	// 					</tr>
	// 				</table>";
$header__ = '<table ><thead>
			<tr style="border: 1;">
				<th colspan="2" align="left">
	                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
	                style="padding-left:10px;height:50px; width:auto;" /></a>
	            </th>
				<th align="left" colspan="6" class="f12" style="border-right: 0;">
					<span style="font-size: 1.5em;"><b>' . $title . '</b></span>
					 <br> <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$toppic.'</b>
				</th>

			</tr>
			<tr style="border: 1;">

        <th colspan="4" align="lift">
          <b>
            DATE:
            '. date('d-m-Y', strtotime($create_date)).'

              <BR><BR> Return requested by :'. $nameUser .'
            </b>
					</th>
              <th colspan="4" align="left">

                  <b>
                    Return No :'. $journalId .'

                    <BR><BR> Refer to Withdrawal No :'. $Ref .'
                  </b>
								</th>

			</tr>
		</thead>
	</table>';

$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3,20);
$pdf->SetDisplayMode('fullpage');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Tire Return</title>
	<style type="text/css">
		table {
		    border-collapse: collapse;
		    width: 100%;
		    font-size: 10px;
		}

		td, tr {
		    border: 1px solid #000000
		;
		    text-align: left;
		    padding: 5px;
		}
		.f12{
			font-size:14px;
		    font-family:"Angsana New";
		}

		th {
			padding: 5px;
		}

	</style>
</head>
<body >
	<div class="container">
		<table>
			<thead>
			<tr>
				<th colspan="2" align="left">
	                <a class="navbar-brand"><img  src="./assets/images/STR.jpg"
	                style="padding-left:10px;height:50px; width:auto;" /></a>
	            </th>
				<th align="center" colspan="6" class="f12" style="border-right: 0;">
					<span style="font-size: 1.5em;"><b><?php echo $title; ?></b></span>
					 <br> <b><?php echo $toppic ?></b>
				</th>

			</tr>
			<tr>

        <th colspan="4" align="lift">
          <b>
            DATE:
            <?php
              echo date('d-m-Y', strtotime($create_date));
            ?>
              <BR><BR> Return requested by :<?php echo $nameUser ?>
            </b>
					</th>
              <th colspan="4" align="left">

                  <b>
                    Return No : <?php echo $journalId ?>

                    <BR><BR> Refer to Withdrawal No : <?php echo $Ref;?>
                  </b>
								</th>

			</tr>
      <tr>
					<th align="center" width="3%" style="border: 1px solid #000000;">
						<b>No.</b></th>
					<th align="center" width="7%" style="border: 1px solid #000000;">
						<b>Curing Code</b></th>
					<th align="center" width="10%" style="border: 1px solid #000000;">
						<b>Item</b></th>
					<th align="center" width="40%" style="border: 1px solid #000000;">
						<b>Name</b></th>
					<th align="center" width="10%" style="border: 1px solid #000000;">
						<b>Batch</b></th>
					<th align="center" width="10%" style="border: 1px solid #000000;">
						<b>Quantity</b></th>
					<th align="center" width="20%" style="border: 1px solid #000000;">
						<b>Remarks</b></th>
				</tr>
			</thead>
			<?php
				// echo "<pre>" . print_r($datajson, true) . "</pre>"; exit;
				$i = 1;
				foreach ($datajson as  $value) {

			?>
      <tr>
				<td align="center">
					<?php echo $i; ?>
				</td>
				<td align="center">
					<?php
						echo $value->CuringCode;
					?>
				</td>
				<td align="center">
					<?php
						//echo $value->time_create;
						echo $value->ItemID;
					?>
				</td>
        <td align="left">
          <?php
            echo $value->NameTH;
          ?>
        </td>
				<td align="center">
					  <?php if ($value->Batch !== '' && $value->Batch !== null) {
                  echo $value->Batch;
            } ?>
				</td>
        <td align="center">
					<?php if ($value->TotalQty!='') {
							echo $value->TotalQty;
							}else{
							echo "<br>";
							}
					?>
				</td>
				<td align="center">
					<?php
								echo $value->Description;
					 ?>
				</td>


			</tr>
			<?php
				$pdf->SetHTMLFooter($footer);
				$i++;
				}
			?>
			<tr>
				<td colspan="5" align ="right">

          <b>Total : </b>
          <?php
            $sum = 0;
            foreach ($datajson as $value) {
              $sum += $value->TotalQty;
            }
            echo $sum . "  <b>เส้น</b>";
          ?>

				</td>
				<td colspan="3">

				</td>
			</tr>
		</table>

	</div>
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();

$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);
// $a = "{PAGENO}";
//

if ( $a > 12) {
	$pdf->AddPage();
	 $pdf->WriteHTML($header__);
}


$pdf->WriteHTML('
	<br>
		<table border="0" cellpadding="10" autosize="2.4">


       	<tr style="border: 0;">
					<td width="50%" style="border: 0;">
						Return by : _________________________________________
					</td>
					<td width="50%" style="border: 0;">
					Received by : _________________________________________
					</td>

       	</tr>
       	<tr style="border: 0;">
					<td style="border: 0;">
						_________________________________________<br><br>Plant Q-Tech Division Head
					</td>
					<td style="border: 0;">
						_________________________________________<br><br>Business Team Manager
					</td>

       	</tr>

        <tr style="border: 0;">
          <td style="border: 0;">
            _________________________________________<br><br>Plant Q-Tech Manager
          </td>
          <td style="border: 0;">
            _________________________________________<br><br>Plant Manager
          </td>

        </tr>
    </table>');

$pdf->Output();
