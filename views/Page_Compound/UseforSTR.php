<?php $this->layout("layouts/base", ['title' => 'Tranfer SVO  (Move Stock คืน Pallet)']); ?>

<div class="head-space"></div>
 <!-- form genarator -->
<div class="panel panel-default form-center">
	<div class="panel-heading" style="background-color:#3399ff;">Tranfer SVO  (Move Stock คืน Pallet)</div>
  <div class="panel-body">
	   <form id = "pressform">
        <div class="form-group row">

         <label for="id" class="col-sm-3 col-form-label">compound Code.</label>
         <div class="col-sm-5">
           <input type="text" class="form-control" name="Pallet" id="Pallet" required >

         </div>
       </div>

      Location : XXX ,XXX XXX ,XXX
			<div class="form-group row">

			 <label for="id" class="col-sm-3 col-form-label">QTY</label>
			 <div class="col-sm-5">
				 <input type="text" class="form-control" name="Pallet" id="Pallet" required >

			 </div>
		 </div>
      <div class="form-group row">

       <label for="id" class="col-sm-3 col-form-label">Mc. Receive</label>
       <div class="col-sm-5">
         <input type="text" class="form-control" name="Pallet" id="Pallet" required >

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
