<?php $this->layout("layouts/base", ['title' => 'Hold']); ?>

<div class="head-space"></div>
 <!-- form genarator -->
<div class="panel panel-default form-center">
	<div class="panel-heading" style="background-color:#3399ff;">Hold</div>
  <div class="panel-body">
	   <form id = "pressform">
        <div class="form-group row">

         <label for="id" class="col-sm-3 col-form-label">Weight</label>
         <div class="col-sm-5">
           <input type="text" class="form-control" name="description" id="ML" required >

         </div>
       </div>
       <div class="form-group row">
         <input type="hidden" name="PalletID" id="PalletID" class="form-control" required>
         <label for="id" class="col-sm-3 col-form-label">Pallet ID</label>
         <div class="col-sm-5">
           <input type="text" class="form-control" name="PalleID" id="PalleID" required >

         </div>
       </div>
       <div class="form-group row">

         <label for="id" class="col-sm-3 col-form-label">New Pallet ID</label>
         <div class="col-sm-5">
           <input type="text" class="form-control" name="Defect" id="Defect" required >

         </div>
       </div>

          <input type="submit" style="visibility: hidden;" id="subformAddPaleet" name="subformAddPaleet" />
	    </form>
  </div>
</div>




<script>
	jQuery(document).ready(function($) {

    $('#pressform').on('submit', function(event){
          event.preventDefault();

          var PalleID = $('#PalleID').val();
          var ML = $('#ML').val();
          alert(PalleID);

if (!!$.trim(PalleID) && !!$.trim(ML)) {

  alert("Nopress");


  } else {
    $('#modal_alert').modal({backdrop: 'static'});
    $('#modal_alert_message').text('กรุณากรอกข้อมูล');
    $('#top_alert').hide();
  }
  });


		});




</script>
