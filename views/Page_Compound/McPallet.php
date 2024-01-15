<?php $this->layout("layouts/base", ['title' => 'Barcode Curing']); ?>
<?php ob_start(); ?>
<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Select MC.</div>
  <div class="panel-body">
      <input type="hidden" name="dataMc" id="dataMc" class="form-control" value="<?php echo $McID ?>"required readonly>
    <form id="formPallet">
      <div class="form-group row">

        <label for="id" class="col-sm-4 col-form-label">Mc. </label>
        <div class="col-sm-4">
        <input type="text" name="aa" id="aa" class="form-control" required readonly>
				  <input type="hidden" name="statuscheck" id="statuscheck" value="0" class="form-control" required readonly>
      </div>
      </div>
      <div class="form-group row">

        <label for="id" class="col-sm-4 col-form-label">compound Code: </label>

        <div class="col-sm-4">

        <input type="text" name="Compound_Code" id="Compound_Code" class="form-control" required readonly>
      </div>
      </div>
      <div class="form-group row">

        <label for="id" class="col-sm-4 col-form-label">Pallet:</label>
        <div class="col-sm-4">
        <input type="text" name="Pallet" id="Pallet" class="form-control" required readonly>
      </div>
      </div>

    <div class="form-group row ">

       <label for="id" class="col-sm-4 col-form-label">Weight</label>
      <div class="col-sm-4">
      <input type="text" name="Weight" id="Weight" class="form-control" pattern="[1-9][.][0-9]{1}" required autofocus >
    <input type="hidden" name="data_date" id="data_date"  class="form-control" required >
    </div>
    </div>
    <div class="form-group row ">

       <label for="id" class="col-sm-4 col-form-label">Pallet ID</label>
      <div class="col-sm-4">
      <input type="text" name="PalleID" id="PalleID" class="form-control" pattern="[0-9]{1,}" required  >
    </div>
    </div>
    <br>
    <input type="submit" style="visibility: hidden;" id="subformAddPaleet" name="subformAddPaleet" />
    <!-- <button class="btn btn-danger btn-lg" type="reset">Clear</button> -->
  <center>  <button class="btn btn-warning btn-lg" type="button" id="close_model" name="close_model" >Cancel</button></center>
  </form>
  </div>
</div>

<div class="modal" id="checklogin" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Login</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->

          <div class="panel-body">
            <form id="logincheck">
           <div class="form-group">
             <label for="authen_code">Username</label>
             <input type="text" id="authen_code" name="authen_code" class="form-control input-lg inputs" placeholder="Username" required>
           </div>
           <div class="form-group">
             <label for="authen_pass">Password<?php echo $_SESSION['user_login'] ?></label>
             <input type="password" id="authen_pass" name="authen_pass" class="form-control input-lg inputs" required placeholder="password">
           </div>
           <input type="submit" style="visibility: hidden;" id="subform" name="subform" />

         </form>
          </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
 <div class="modal-dialog modal-sm">
	 <div class="modal-content">
		 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			 <h4 class="modal-title" id="myModalLabel">are you sure ?</h4>

		 </div>
		 <b class="col-sm-6 col-form-label">Mc:</b><MC_text class="col-sm-4"></MC_text><br>
		 <b class="col-sm-6 col-form-label">compound Code:</b><compoundc class="col-sm-4"></compoundc><br>

		 <div class="modal-footer">
		 <button type="button" class="btn btn-default" id="modal-btn-si">Yes</button>
			 <button type="button" class="btn btn-primary" id="modal-btn-no">No</button>
		 </div>
	 </div>
 </div>
</div>







<script>
jQuery(document).ready(function($) {

		var McID =  $('#dataMc').val();
			gojax_f('post', base_url+'/api/Compound/tb/'+ McID)
  		.done(function(data) {
    	if (data.status === 200) {

					$('input[name=aa]').val(data.McID);
       		$('input[name=Compound_Code]').val(data.CompoundCodeID);
        	$('input[name=Pallet]').val(data.total_Pallet);
      		$('input[name=data_date]').val(data.datecreadte);
      		$("MC_text").text(data.McID);
      		$("compoundc").text(data.CompoundCodeID);
				} else {

      		$('#modal_alert').modal({backdrop: 'static'});
      		$('#modal_alert_message').text(data.message);
      		$('#top_alert').hide();

    					}
  				});

  		$('#formPallet').on('submit', function(event){
    				event.preventDefault();

    				var PalleID = $('#PalleID').val();
    				var Weight = $('#Weight').val();
						if (!!$.trim(PalleID) && !!$.trim(Weight)) {
							gojax_f('post', base_url+'/api/Compound/savePallet', '#formPallet')
      				.done(function(data) {
								if (data.status === 200)  {
										//alert(1111);
										//	$('#grid_movementType').jqxGrid('updatebounddata');
          				//$('#modal_create').modal('hide');
									$('input[name=PalleID]').val('');
									$('input[name=Weight]').val('');
									$('input[name=Pallet]').val(data.totalPallet);
									$('#modal_Successful').modal({backdrop: 'static'});
									$('#modal_Successful_message').text(data.totalPallet);
								} else {
									gojax_f('post', base_url+'/api/Compound/tb/'+ McID)
						 			.done(function(data) {
							 //	alert(data.total_Pallet);
							 		$('input[name=aa]').val(data.McID);
									$('input[name=Compound_Code]').val(data.CompoundCodeID);
									$('input[name=Pallet]').val(data.total_Pallet);
									$('input[name=data_date]').val(data.datecreadte);
									$("MC_text").text(data.McID);
									$("compoundc").text(data.CompoundCodeID);
									if(data.total_Pallet== null)
									 {
										 $('#modal_alert').modal({backdrop: 'static'});
							       $('#modal_alert_message').text("ไม่มีPallet");

									 }
									else{
										$('#modal_warning').modal({backdrop: 'static'});
										$('#modal_warning_message').text("กำลังใช้ Pallet ต่อไป");
									}});
								}
      					});
							} else {
      						$('#modal_alert').modal({backdrop: 'static'});
      						$('#modal_alert_message').text('กรุณากรอกข้อมูล');
      						$('#top_alert').hide();
    				}
  					});
							// Comment_js
							$('#close_model').on('click', function(event) {
    					event.preventDefault();
							//	$('#insertdata').modal('hide');
  						$('#checklogin').modal({backdrop: 'static'});
							$('input[name=authen_code]').focus();
							});
							$('#checklogin').on('submit', function(event) {
							event.preventDefault();
							gojax('post', base_url+'/apt/authorize/type', {type: 'unhold_unrepair'})
							.done(function(data) {
							gojax('post', base_url+'/api/user/authorize', {
							code: $('#authen_code').val(),
							password: $('#authen_pass').val(),
							type: data.type
							})
							.done(function(data) {
							if (data.status == 200) {
							//	alert(1234);
							$('#checklogin').modal('hide');
							$('#logincheck').trigger('reset');
							$("#mi-modal").modal('show');
							$("#modal-btn-si").on("click", function(){
							$('input[name=statuscheck]').val(1);
		    			$("#mi-modal").modal('hide');
				 			$('#modal_Successful').modal({backdrop: 'static'});
							$('#modal_Successful_message').text('Successful');
							gojax_f('post', base_url+'/api/Compound/savePallet', '#formPallet')
							.done(function(data) {
							});
							setTimeout(function() {
	    				$('#modal_Successful').modal('hide');
							}, 3500);
							window.location.reload()
							//	window.close();
							});
							$("#modal-btn-no").on("click", function(){
 							$("#mi-modal").modal('hide');
		  				});
							} else {
							$('#logincheck').trigger('reset');
							alert(data.message);
							}
							});
							})
							.fail(function() {
								$('#modal_alert').modal({backdrop: 'static'});
								$('#modal_alert_message').text('Cannot send data to server!');
								$('#top_alert').hide();
							});
						});
					});


</script>
