<?php $this->layout("layouts/base", ['title' => 'Barcode Curing']); ?>

<div class="head-space"></div>
 <!-- form genarator -->
<div class="panel panel-default form-center">
	<div class="panel-heading" style="background-color:#3399ff;">Pass(Ok)</div>
  <div class="panel-body">
	   <form id = "pressform">
        <div class="form-group row">

         <label for="id" class="col-sm-3 col-form-label">ML.</label>
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

  alert(111);


  } else {
    $('#modal_alert').modal({backdrop: 'static'});
    $('#modal_alert_message').text('กรุณากรอกข้อมูล');
    $('#top_alert').hide();
  }
  });


		});




</script>
