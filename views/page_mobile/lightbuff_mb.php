<?php $this->layout("layouts/handheldmobile", ["title" => "Mode Light Buff"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;" id="fromserchWithdrawal">
	<div class="panel-heading">Mode Light Buff</div>
	<div class="panel-body">
		<form id="form_tracking">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="barcode" id="barcode" placeholder="Barcode" required>
			</div>
		</form>

	</div>
</div>


<script>
	jQuery(document).ready(function($) {

		var barcode = $("input[name=barcode]");
		barcode.val('').focus();
    $('#form_tracking').submit(function(e) {
			 e.preventDefault();
            if ($.trim(barcode.val()) !== '') {
			 		gojax_f('post', base_url+'/api/lightbuff/save', '#form_tracking')
			 		.done(function(data) {
					    if (data.status == 200) {
                            alert(data.message);
                            $('#barcode').val('').focus();
					    } 
                        else {
                            alert(data.message);
                            $('#barcode').val('').focus();
                        }
                    });
                }else{
                    alert("กรุณากรอกข้อมูลให้ครบถ้วน");
                    $('#barcode').val('').focus();
                }
           
        });
    });


    
 

  



</script>
