<?php $this->layout("layouts/base", ['title' => 'Rate Master']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<style>
	td {
		padding: 5px;
	}
</style>
<div class="head-space"></div>
<div class="panel panel-default" id="panel_manage">

	<div class="panel-heading">
 		กำหนดเงื่อนไขการคำนวณค่าเรท
	</div>

    <div class="panel-body">
        <div class="panel-group">
            <div class="form-group">
				<label for="line">Group :  </label>
				<select name="selectMenu[]" id="selectMenu"  multiple="multiple" style="width: 150px">
				</select>
            </div>
        </div>

		<div class="panel-group">
            <div class="form-group">
                <input type="button" name="type_build" id="type_build" class="btn btn-primary btn-sm" value="Builder" style="width:100px">
                <input type="button" name="type_change" id="type_change" class="btn btn-primary btn-sm" value="Change Code" style="width:100px">
            </div>
        </div>
		
		<h4><span id="grouptxt" ></span><span id="subgrouptxt" ></span></h4>
		<div class="panel-group">
            <div class="form-group">
				<button class="btn btn-success" id="create"><span class="glyphicon glyphicon-plus"></span> Create New</button>
				<button class="btn btn-info" id="edit"><span class="glyphicon glyphicon-edit"></span> Edit </button>
            </div>
        </div>
		
        <div id="grid_rate"></div>
        
    </div>

</div>

<div class="modal" id="modal_add_builder" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:700px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">สร้างเงื่อนไขใหม่</h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_add_builder">

					<table>
						<tr>
							<td style="text-align: right;" >เครื่อง :</td>
							<td>
								<select name="selectMachine[]" id="selectMachine"  multiple="multiple" 
								style="width: 150px" required>
							</td>
							<td style="text-align: right;" >PLY :</td>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" name="ply" id="ply" style="width: 100px" readonly>
									<span class="input-group-btn">
										<button class="btn btn-info" id="select_ply" type="button">
											<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
										</button>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">1.สร้างยางครบ :</td>
							<td>
								<input type="text" name="Qty1" id="Qty1" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;">ยอดจ่าย :</td>
							<td>
								<input type="text" name="RatePrice1" id="RatePrice1" autocomplete="off" class="form-control" required>
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">2.สร้างยางตั้งแต่ :</td>
							<td>
								<input type="text" name="Qty2" id="Qty2" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;">จ่ายเส้นละ :</td>
							<td>
								<input type="text" name="RatePrice2" id="RatePrice2" autocomplete="off" class="form-control">
							</td>
						</tr>
						<tr>
							<td style="text-align: right;"><span id="HeadQty3" ></span></td>  
							<!--3.สร้างยางตั้งแต่ :  -->
							<td>
								<input type="text" name="Qty3" id="Qty3" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;"><span id="HeadRatePrice3" ></span></td>
							<!-- จ่ายเส้นละ : -->
							<td>
								<input type="text" name="RatePrice3" id="RatePrice3" autocomplete="off" 
								class="form-control"  required>
							</td>
						</tr>
					</table>

					<table>
						<tr>
							<td  style="text-align: right;width: 110px">หมายเหตุ : </td>
							<td>
								<input type="text"  name="remark" id="remark" autocomplete="off" 
								class="form-control" style="width: 300px"  >
							</td>
							<td>
								<button type="submit" id="btnAddBuild" class="btn btn-primary">
									<span class="glyphicon glyphicon-download"></span>
									บันทึกข้อมูล
								</button> 
							</td>
						</tr>
					</table>

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 300px; margin: auto;">
									<div class="panel-heading">
										Condition Rate Build (TBR/Builder)
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_tbr.png">
									</div>
								</div>
							</td>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 350px; margin: auto;">
									<div class="panel-heading">
										Condition Rate Build (PCR/Builder)
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_pcr.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 650px; margin: auto;">
									<div class="panel-heading">
										การใส่ข้อมูลของเครื่องที่เป็น TBR ตำแหน่ง Builder
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_tbr2.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 650px; margin: auto;">
									<div class="panel-heading">
										การใส่ข้อมูลของเครื่องที่เป็น PCR ตำแหน่ง Builder
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_pcr2.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

				</form>
      		</div>

		</div>
	</div>
</div>

<div class="modal" id="modal_add_changecode" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:350px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">สร้างเงื่อนไขใหม่</h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_add_changecode">
				<div class="panel-body">
					<div class="panel-group">
						<div class="form-group">
							<label for="selectMachine2">เครื่อง :  </label><br/>
							<select name="selectMachine2[]" id="selectMachine2"  
							multiple="multiple" style="width: 150px" required>
							</select>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<label for="ply2">PLY :</label>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12" style="width: 190px"> 
									<div class="input-group">
									<input type="text" class="form-control" name="ply2" id="ply2" readonly>
									<span class="input-group-btn">
										<button class="btn btn-info" id="select_ply2" type="button">
											<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
										</button>
									</span>
									</div>
								</div>
							</div>
						</div>
					

						<div class="form-group">
							<label for="CQty1">สร้างยางครบ (เส้น) </label>
							<input type="text" name="CQty1" id="CQty1" class="form-control"
							style="width: 150px" autocomplete="off" required>
						</div>

						<div class="form-group">
							<label for="CRatePrice1">เหมาจ่าย/วัน </label>
							<input type="text" name="CRatePrice1" id="CRatePrice1" class="form-control"
							style="width: 150px" autocomplete="off" required>
						</div>

						<div class="form-group">
							<label for="Cremark">หมายเหตุ </label>
							<input type="text" name="Cremark" id="Cremark" class="form-control"
							style="width: 300px" autocomplete="off" >
						</div>
					</div>
				</div>

				<button type="submit" id="btnAddChange" class="btn btn-block btn-lg btn-primary">
					<span class="glyphicon glyphicon-download"></span>
					บันทึกข้อมูล
            	</button>
				</form>
      		</div>

		</div>
	</div>
</div>

<div class="modal" id="modal_edit_builder" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:700px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">แก้ไขข้อมูล เครื่อง : 
				  <span id="modal_mc" ></span>
				  <span id="plyHeader" >PLY : </span>
				  <span id="ply" ></span>
				</h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_edit_builder">

					<table>
						<tr>
							<td style="text-align: right;">1.สร้างยางครบ :</td>
							<td>
								<input type="text" name="EQty1" id="EQty1" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;">ยอดจ่าย :</td>
							<td>
								<input type="text" name="ERatePrice1" id="ERatePrice1" autocomplete="off" class="form-control" required>
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">2.สร้างยางตั้งแต่ :</td>
							<td>
								<input type="text" name="EQty2" id="EQty2" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;">จ่ายเส้นละ :</td>
							<td>
								<input type="text" name="ERatePrice2" id="ERatePrice2" autocomplete="off" class="form-control" required>
							</td>
						</tr>
						<tr>
							<td style="text-align: right;"><span id="EHeadQty3" ></span></td>  
							<!--3.สร้างยางตั้งแต่ :  -->
							<td>
								<input type="text" name="EQty3" id="EQty3" autocomplete="off" class="form-control" required>
							</td>
							<td style="text-align: right;"><span id="EHeadRatePrice3" ></span></td>
							<!-- จ่ายเส้นละ : -->
							<td>
								<input type="text" name="ERatePrice3" id="ERatePrice3" autocomplete="off" 
								class="form-control"  required>
							</td>
						</tr>
					</table>

					<table>
						<tr>
							<td  style="text-align: right;width: 110px">หมายเหตุ : </td>
							<td>
								<input type="text"  name="Eremark" id="Eremark" autocomplete="off" 
								class="form-control" style="width: 300px" >
							</td>
							<td>
								<button type="submit" id="btnEditBuild" class="btn btn-primary">
									<span class="glyphicon glyphicon-download"></span>
									บันทึกข้อมูล
								</button> 
							</td>
						</tr>
					</table>
					<input type="hidden" name="Eid" id="Eid">

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 300px; margin: auto;">
									<div class="panel-heading">
										Condition Rate Build (TBR/Builder)
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_tbr.png">
									</div>
								</div>
							</td>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 350px; margin: auto;">
									<div class="panel-heading">
										Condition Rate Build (PCR/Builder)
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_pcr.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 650px; margin: auto;">
									<div class="panel-heading">
										การใส่ข้อมูลของเครื่องที่เป็น TBR ตำแหน่ง Builder
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_tbr2.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

					<table  style="width: 650px;">
						<tr>
							<td valign="top">
								<div class="panel panel-info" style="max-width : 650px; margin: auto;">
									<div class="panel-heading">
										การใส่ข้อมูลของเครื่องที่เป็น PCR ตำแหน่ง Builder
									</div>
									<div class="panel-body">
										<img src="/resources/example/ratemaster_build_pcr2.png">
									</div>
								</div>
							</td>
						</tr>
					</table>

				</form>
      		</div>

		</div>
	</div>
</div>

<div class="modal" id="modal_edit_changecode" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:350px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">แก้ไขข้อมูล เครื่อง : <span id="modal_Cmc" ></span></h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_edit_changecode">
				<div class="panel-body">
					<div class="panel-group">

						<div class="form-group">
							<label for="ECQty1">สร้างยางครบ (เส้น) </label>
							<input type="text" name="ECQty1" id="ECQty1" class="form-control"
							style="width: 100px" autocomplete="off" required>
						</div>

						<div class="form-group">
							<label for="ECRatePrice1">เหมาจ่าย/วัน </label>
							<input type="text" name="ECRatePrice1" id="ECRatePrice1" class="form-control"
							style="width: 100px" autocomplete="off" required>
						</div>

						<div class="form-group">
							<label for="Cremark">หมายเหตุ </label>
							<input type="text" name="ECremark" id="ECremark" class="form-control"
							style="width: 300px" autocomplete="off" >
						</div>

						<input type="hidden" name="ECid" id="ECid">
					</div>
				</div>

				<button type="submit" id="btnEditChange" class="btn btn-block btn-lg btn-primary">
					<span class="glyphicon glyphicon-download"></span>
					บันทึกข้อมูล
            	</button>
				</form>
      		</div>

		</div>
	</div>
</div>

<div class="modal" id="modal_select_ply" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" style="width:200px;margin:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select PLY</h4>
      </div>
      <div class="modal-body">
        <div id="grid_ply"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="modal_select_ply2" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" style="width:200px;margin:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select PLY</h4>
      </div>
      <div class="modal-body">
        <div id="grid_ply2"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="modal_add_cure" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:350px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">สร้างเงื่อนไขใหม่</h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_add_cure">
					
					<div class="panel-body">
						<div class="panel-group">
							
							<div class="form-group">
								<label for="selectLine">Line :  </label><br/>
								<select name="selectLine[]" id="selectLine"  
								multiple="multiple" style="width: 150px" required>
								</select>
							</div>

							<div class="form-group" style="display: block;">
								<label for="curetype">เลือกType :  </label><br/>
								<label style="padding-left: 40px;">
									<input type="radio" name="curetype" value="TBR" checked/> TBR
								</label>
								<label style="padding-left: 40px;">
									<input type="radio" name="curetype" value="PCR" /> PCR
								</label>
								<!-- <label style="padding-left: 40px;">
									<input type="radio" name="curetype" value="BIA" /> Bias
								</label> -->
							</div>

							<div class="form-group">
								<label for="CurePrice">เมื่อทำครบตามเงื่อนไขจ่าย : </label>
								<input type="text" name="CurePrice" id="CurePrice" class="form-control"
								style="width: 250px" autocomplete="off" required>
							</div>
						</div>
					</div>

					<button type="submit" id="btnAddCure" class="btn btn-block btn-lg btn-primary">
						<span class="glyphicon glyphicon-download"></span>
						บันทึกข้อมูล
					</button>
				</form>
      		</div>

		</div>
	</div>
</div>

<div class="modal" id="modal_edit_cure" tabindex="-1" role="dialog">
  	<div class="modal-dialog modal-lg" role="document" style="width:350px;margin:auto;">
    	<div class="modal-content">

	  		<div class="modal-header">
          		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            		<span aria-hidden="true">&times;</span>
          		</button>
          		<h4 class="modal-title">แก้ไขข้อมูล เครื่อง : <span id="modal_Curemc" ></span></h4>
      		</div>
			
			<div class="modal-body">	
				<form id="form_edit_cure">
				<div class="panel-body">
					<div class="panel-group">

						<div class="form-group" style="display: block;">
								<label for="Ecuretype">เลือกType :  </label><br/>
								<label style="padding-left: 40px;">
									<input type="radio" id="Ecuretype1" name="Ecuretype" value="TBR" /> TBR
								</label>
								<label style="padding-left: 40px;">
									<input type="radio" id="Ecuretype2" name="Ecuretype" value="PCR" /> PCR
								</label>
								<!-- <label style="padding-left: 40px;">
									<input type="radio" id="Ecuretype3" name="Ecuretype" value="BIA" /> Bias
								</label> -->
						</div>

						<div class="form-group">
								<label for="ECurePrice">เมื่อทำครบตามเงื่อนไขจ่าย : </label>
								<input type="text" name="ECurePrice" id="ECurePrice" class="form-control"
								style="width: 250px" autocomplete="off" required>
						</div>

						<!-- <input type="hidden" name="ECid" id="ECid"> -->
					</div>
				</div>

				<button type="submit" id="btnEditCure" class="btn btn-block btn-lg btn-primary">
					<span class="glyphicon glyphicon-download"></span>
					บันทึกข้อมูล
            	</button>
				</form>
      		</div>

		</div>
	</div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function($) 
	{

		$('#type_build').prop('disabled',true);
		$('#type_change').prop('disabled',false);
		$('#selectMenu').html("");
		$('#create').hide();
		$('#edit').hide();

		setInt('#Qty1');
		setInt('#Qty2');
		setInt('#Qty3');
		setInt('#RatePrice1');
		setInt('#RatePrice2');
		setInt('#RatePrice3');

		setInt('#CQty1');
		setInt('#CRatePrice1');

		setInt('#EQty1');
		setInt('#EQty2');
		setInt('#EQty3');
		setInt('#ERatePrice1');
		setInt('#ERatePrice2');
		setInt('#ERatePrice3');

		setInt('#ECQty1');
		setInt('#ECRatePrice1');

		setInt('#CurePrice');

		curetype = "TBR";

		getRateGroup()
		.done(function(data) {
			$.each(data, function(k, v) {
				$('#selectMenu').append('<option value="'+ v.ID +'" >'+v.Description+'</option>');
			});
			$('#selectMenu').val(1);
			$('#selectMenu').multipleSelect({single: true});
			$('#grouptxt').text("Build");
		});
		
		$('#selectMenu').on('change', function()
		{
			if (this.value === '1')
			{
				$('#type_build').show();
				$('#type_change').show();
				$('#type_build').prop('disabled',false);
				$('#type_change').prop('disabled',false);
				$('#grouptxt').text("Build");
				$('#create').hide();
				$('#edit').hide();
			}
			else
			{
				$('#type_build').hide();
				$('#type_change').hide();
				$('#grouptxt').text("Cure");
				$('#subgrouptxt').text("");
				$('#create').show();
				$('#edit').show();
				bindGrid_Cure();
			}
			
			
		});

		$('#type_build').on('click', function() 
		{
			$('#type_build').prop('disabled',true);
			$('#type_change').prop('disabled',false);
			$('#subgrouptxt').text(" / Builder");
			$('#create').show();
			$('#edit').show();
			bindGrid_Build_1();
			
		});

		$('#type_change').on('click', function() 
		{
			$('#type_build').prop('disabled',false);
			$('#type_change').prop('disabled',true);
			$('#subgrouptxt').text(" / Change Code");
			$('#create').show();
			$('#edit').show();
			bindGrid_Build_2();
			
		});

		$('#create').on('click',function()
    	{
				
			$('#selectMachine').html("");
			$('#selectMachine2').html("");
			$('#select_ply').prop('disabled',true);
			$('#select_ply2').prop('disabled',true);

			$('#selectLine').html("");

			if($('#selectMenu').val() == '1' && $('#type_build').prop('disabled')== true)
			{
				getMachine("1")
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#selectMachine').append('<option value="'+ v.Machine +'" >'+v.Machine+'</option>');
					});
					$('#selectMachine').multipleSelect({single: true});
				});

				$('#modal_add_builder').modal({backdrop: 'static'});
				$("#form_add_builder")[0].reset();
			}
			else if ($('#selectMenu').val() == '1' && $('#type_build').prop('disabled')== false)
			{
				getMachine("2")
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#selectMachine2').append('<option value="'+ v.Machine +'" >'+v.Machine+'</option>');
					});
					$('#selectMachine2').multipleSelect({single: true});
				});

				$('#modal_add_changecode').modal({backdrop: 'static'});
				$("#form_add_changecode")[0].reset();
			}
			else if ($('#selectMenu').val() == '2')
			{
				getMac()
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#selectLine').append('<option value="'+ v.Line +'" >'+v.Line+'</option>');
					});
					$('#selectLine').multipleSelect({single: true});
				});

				$('#modal_add_cure').modal({backdrop: 'static'});
				$("#form_add_cure")[0].reset();
			}

		});

		$('#edit').on('click',function()
    	{
			event.preventDefault();
			var rowdata = row_selected('#grid_rate');
			if (typeof rowdata !== 'undefined') 
			{
				if($('#selectMenu').val() == '1' && $('#type_build').prop('disabled')== true)
				{
					$('#modal_edit_builder').modal({backdrop: 'static'});
					machine = rowdata.Machine;
					id = rowdata.id;
					qty1 = rowdata.Qty1;
					qty2 = rowdata.Qty2;
					qty3 = rowdata.Qty3;
					ratep1 = rowdata.RatePrice1;
					ratep2 = rowdata.RatePrice2;
					ratep3 = rowdata.RatePrice3;
					remark = rowdata.Remark;
					ply = rowdata.PLY;


					$('#modal_mc').text(machine);
					$('#Eid').val(id);

					$('#EQty1').val(qty1);
					$('#EQty2').val(qty2);
					$('#EQty3').val(qty3);
					$('#ERatePrice1').val(ratep1);
					$('#ERatePrice2').val(ratep2);
					$('#ERatePrice3').val(ratep3);
					$('#Eremark').val(remark);

					gojax_f('post', base_url+'/api/ratemaster/getMachineType/'+ machine)
					.done(function(data) 
					{
						if (data.status == 200) 
						{
							$('#EHeadQty3').text("3.สร้างยางตั้งแต่ :");	
							$('#EHeadRatePrice3').text("จ่ายเส้นละ :");
							$('#ELastRatePrice3').text("");	
							$('#plyHeader').text("");
							$('#ply').text("");
						} 
						else 
						{
							$('#EHeadQty3').text("3.สร้างยางครบ(1)จ่าย : ");
							$('#EHeadRatePrice3').text("เส้น/(บาท) :");
							$('#ELastRatePrice3').text(" บาท");	

							if(machine == 'VMI01' || machine == 'VMI02' )
							{	
								$('#plyHeader').text("");
								$('#ply').text("");
							}
							else
							{
								$('#plyHeader').text(" | PLY : ");
								$('#ply').text(ply);
							}
						}
					});
				}

				else if ($('#selectMenu').val() == '1' && $('#type_build').prop('disabled')== false)
				{
					$('#modal_edit_changecode').modal({backdrop: 'static'});
					Cmachine = rowdata.Machine;
					Cid = rowdata.id;
					Cratep1 = rowdata.RatePrice1;
					Cremark = rowdata.Remark;
					Cqty1 = rowdata.Qty1;

					$('#modal_Cmc').text(Cmachine);
					$('#ECid').val(Cid);
					$('#ECremark').val(Cremark);
					
					$('#ECQty1').val(Cqty1);
					$('#ECRatePrice1').val(Cratep1);
					
				}

				else if ($('#selectMenu').val() == '2')
				{

					$('#modal_edit_cure').modal({backdrop: 'static'});
					cureMac = rowdata.Machine;
					curePrice = rowdata.RatePrice1;
					cureType = rowdata.RateType;

					$('#modal_Curemc').text(cureMac);
					$('#ECurePrice').val(curePrice);

					if(cureType === "TBR")
					{
						$('#Ecuretype1').prop('checked', true);
					}
					else if(cureType === "PCR")
					{
						$('#Ecuretype2').prop('checked', true);
					}
					else
					{
						$('#Ecuretype3').prop('checked', true);
					}
					
				}
				
			} 
			else 
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		$('#form_add_builder').on('submit', function(event) 
		{
			$('#modal_add_builder').modal('hide');
			event.preventDefault();

			machine = $('#selectMachine').val();
			qty1 = $('#Qty1').val();
			qty2 = $('#Qty2').val();
			qty3 = $('#Qty3').val();
			ratep1 = $('#RatePrice1').val();
			ratep2 = $('#RatePrice2').val();
			ratep3 = $('#RatePrice3').val();
			remark = $('#remark').val();

			// alert(machine + qty1 + ratep1 + qty2 + ratep2 + qty3 + ratep3 + remark);
			gojax_f('post', base_url+'/api/ratemaster/insertBuild_Builder/'+ machine , '#form_add_builder')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});
		});

		$('#form_add_changecode').on('submit', function(event) 
		{
			$('#modal_add_changecode').modal('hide');
			event.preventDefault();

			cmachine = $('#selectMachine2').val();
			cratep1 = $('#CRatePrice1').val();

			// alert(cmachine + cratep1);
			gojax_f('post', base_url+'/api/ratemaster/insertBuild_ChangeCode/'+ cmachine , '#form_add_changecode')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});
			
		});

		$('#form_edit_builder').on('submit', function(event) 
		{
			$('#modal_edit_builder').modal('hide');
			event.preventDefault();

			Emachine = $('#modal_mc').text();
			Eqty1 = $('#EQty1').val();
			Eqty2 = $('#EQty2').val();
			Eqty3 = $('#EQty3').val();
			Eratep1 = $('#ERatePrice1').val();
			Eratep2 = $('#ERatePrice2').val();
			Eratep3 = $('#ERatePrice3').val();
			Eremark = $('#Eremark').val();
			Eid = $('#Eid').val();

			// alert(Emachine + Eqty1 + Eratep1 + Eqty2 + Eratep2 + Eqty3 + Eratep3 + Eremark + Eid);
			gojax_f('post', base_url+'/api/ratemaster/updateBuild_Builder' , '#form_edit_builder')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});
			
		});

		$('#form_edit_changecode').on('submit', function(event) 
		{
			$('#modal_edit_changecode').modal('hide');
			event.preventDefault();

			Ecmachine = $('#modal_Cmc').text();
			Ecratep1 = $('#ECRatePrice1').val();
			Ecid = $('#ECid').val();
			Ecremark = $('#ECremark').val();

			// alert(Ecmachine + Ecratep1 + Ecid);
			gojax_f('post', base_url+'/api/ratemaster/updateBuild_ChangeCode' , '#form_edit_changecode')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});

			
		});

		$('#selectMachine').on('change', function()
		{
			var mac = this.value;
			
			gojax_f('post', base_url+'/api/ratemaster/getMachineType/'+ mac)
			.done(function(data) 
			{
				if (data.status == 200) 
				{
					$('#HeadQty3').text("3.สร้างยางตั้งแต่ :");	
					$('#HeadRatePrice3').text("จ่ายเส้นละ :");
					$('#LastRatePrice3').text("");

					$('#select_ply').prop('disabled',true);
				} 
				else 
				{
					$('#HeadQty3').text("3.สร้างยางครบ(1)จ่าย : ");
					$('#HeadRatePrice3').text("เส้น/(บาท) :");
					$('#LastRatePrice3').text(" บาท");
					if(mac == "VMI01" || mac == "VMI02" )
					{
						$('#select_ply').prop('disabled',true);
					}
					else
					{
						$('#select_ply').prop('disabled',false);
					}	

				}
			});
		});

		$('#selectMachine2').on('change', function()
		{
			var mac2 = this.value;
			
			gojax_f('post', base_url+'/api/ratemaster/getMachineType/'+ mac2)
			.done(function(data) 
			{
				if (data.status == 200) 
				{
					$('#select_ply2').prop('disabled',true);
				} 
				else 
				{
					if(mac2 == "VMI01" || mac2 == "VMI02" )
					{
						$('#select_ply2').prop('disabled',true);
					}
					else
					{
						$('#select_ply2').prop('disabled',false);
					}	

				}
			});
		});

		$('#select_ply').on('click', function() 
		{
			machine = $('#selectMachine').val();
			$('#modal_select_ply').modal({backdrop:'static'});
			$('#grid_ply').jqxGrid('clearselection');
			bindGrid_PLY(machine);
		});

		$('#grid_ply').on('rowdoubleclick', function() 
		{
			var rowdata = row_selected('#grid_ply');
			$('input[name=ply]').val(rowdata.PLY);
			$('#modal_select_ply').modal('hide');
		});

		$('#select_ply2').on('click', function() 
		{
			machine2 = $('#selectMachine2').val();
			$('#modal_select_ply2').modal({backdrop:'static'});
			$('#grid_ply2').jqxGrid('clearselection');
			bindGrid_PLY2(machine2);
		});

		$('#grid_ply2').on('rowdoubleclick', function() 
		{
			var rowdata = row_selected('#grid_ply2');
			$('input[name=ply2]').val(rowdata.PLY2);
			$('#modal_select_ply2').modal('hide');
		});

		$('#form_add_cure').on('submit', function(event) 
		{
			$('#modal_add_cure').modal('hide');
			event.preventDefault();	

			line = $('#selectLine').val();
			cureprice = $('#CurePrice').val();

			gojax_f('post', base_url+'/api/ratemaster/insertCure/'+ line , '#form_add_cure')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});
			
		});

		$('#form_edit_cure').on('submit', function(event) 
		{
			$('#modal_edit_cure').modal('hide');
			event.preventDefault();	
  
			ECuremachine = $('#modal_Curemc').text();
			EcurePrice = $('#ECurePrice').val();

			gojax_f('post', base_url+'/api/ratemaster/updateCureByMachine/'+ ECuremachine , '#form_edit_cure')
			.done(function(data) 
			{	
				if(data.status === 200)
				{
					$('#grid_rate').jqxGrid('updatebounddata');
				}
				else
				{
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				}
			});
			
		});


		function getRateGroup() 
		{
			return $.ajax({
				url : base_url + '/api/ratemaster/rategroup',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function bindGrid_Build_1() 
		{
			var type = $('#selectMenu').val();
			
			if ($('#type_build').prop('disabled') === true) 
			{
				buildtype = 1;
			}
			else 
			{
				buildtype = 2;
			}

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'Machine', type: 'string'},
				{ name: 'Qty1', type: 'int'},
				{ name: 'Qty2', type: 'int'},
				{ name: 'Qty3', type: 'int'},
				{ name: 'RatePrice1', type: 'int' },
				{ name: 'RatePrice2', type: 'int'},
				{ name: 'RatePrice3', type: 'int' },
				{ name: 'BuildType', type: 'string'},
				{ name: 'id', type: 'int'},
				{ name: 'S2', type: 'string'},
				{ name: 'S3', type: 'string'},
				{ name: 'Remark', type: 'string'},
				{ name: 'PLY', type: 'int'},
				{ name: 'RateType', type: 'string'}
				
				],
				url: base_url + "/api/ratemaster/bindGridBuild1/"+ type + "/" + buildtype
			});

			return $("#grid_rate").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
				{ text: 'เครื่อง', datafield: 'Machine', width: 100},
				{ text: 'ประเภท', datafield: 'RateType', width: 100},
				{ text: 'สร้างยางครบ', datafield: 'Qty1', width: 100},
				{ text: 'ยอดจ่าย', datafield: 'RatePrice1', width: 80},
				{ text: 'สร้างยางเส้นที่', datafield: 'S2', width: 100},
				{ text: 'จ่ายเส้นละ', datafield: 'RatePrice2', width: 80},
				{ text: 'สร้างยางเส้นที่(TBR)/สร้างยางครบตามข้อ1(PCR)', datafield: 'S3', width: 80}, //สร้างยางเส้นที่(TBR)/สร้างยางครบตามข้อ1(PCR)
				{ text: 'จ่ายเส้นละ', datafield: 'RatePrice3', width: 80},
				{ text: 'หมายเหตุ', datafield: 'Remark', width: 250},
				{ text: 'PLY', datafield: 'PLY', width: 80}
				]
			});
		}

		function bindGrid_Build_2() 
		{
			var type = $('#selectMenu').val();
			
			if ($('#type_build').prop('disabled') === true) 
			{
				buildtype = 1;
			}
			else 
			{
				buildtype = 2;
			}

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'Machine', type: 'string'},
				{ name: 'Qty1', type: 'int'},
				{ name: 'Qty2', type: 'int'},
				{ name: 'Qty3', type: 'int'},
				{ name: 'RatePrice1', type: 'int' },
				{ name: 'RatePrice2', type: 'int'},
				{ name: 'RatePrice3', type: 'int' },
				{ name: 'BuildType', type: 'string'},
				{ name: 'id', type: 'int'},
				{ name: 'Remark', type: 'string'},
				{ name: 'PLY' , type: 'int'},
				{ name: 'RateType', type: 'string'}
				],
				url: base_url + "/api/ratemaster/bindGridBuild2/"+ type + "/" + buildtype
			});

			return $("#grid_rate").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
				{ text: 'เครื่อง', datafield: 'Machine', width: 100},
				{ text: 'ประเภท', datafield: 'RateType', width: 100},
				{ text: 'สร้างยางครบ(เส้น)', datafield: 'Qty1', width: 150},
				{ text: 'เหมาจ่าย(บาท)', datafield: 'RatePrice1', width: 100},
				{ text: 'หมายเหตุ', datafield: 'Remark', width: 250},
				{ text: 'PLY', datafield: 'PLY', width: 80}
				]
			});
		}

		function getMachine(buildtype) 
		{
			return $.ajax({
				url : base_url + '/api/ratemaster/getMachine/'+buildtype,
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function bindGrid_PLY(machine) 
		{
			
			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'PLY', type: 'int'}
				],
				url: base_url + "/api/ratemaster/bindGridPLY/"+machine
			});

			return $("#grid_ply").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
					{ text: 'PLY', datafield: 'PLY', width: 100}
				]
			});
		}

		function bindGrid_PLY2(machine2) 
		{
			
			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'PLY2', type: 'int'}
				],
				url: base_url + "/api/ratemaster/bindGridPLY2/"+machine2
			});

			return $("#grid_ply2").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
					{ text: 'PLY', datafield: 'PLY2', width: 100}
				]
			});
		}

		function getMac() 
		{
			return $.ajax({
				url : base_url + '/api/ratemaster/getMac',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

		function bindGrid_Cure() 
		{
			// $("#grid_rate").jqxGrid("clear");
			// $("#grid_rate").jqxGrid("addrow", 0, {});

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
					{ name: 'Machine', type: 'string'},
					{ name: 'RatePrice1', type: 'int' },
					{ name: 'RateType', type: 'string'}
				],
				url: base_url + "/api/ratemaster/bindGridCure"
			});

			return $("#grid_rate").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
				{ text: 'เครื่อง', datafield: 'Machine', width: 100},
				{ text: 'ยอดจ่าย', datafield: 'RatePrice1', width: 80},
				{ text: 'Type', datafield: 'RateType', width: 80}
				]
			});
		}

    });
    
</script>