<?php $this->layout("layouts/handheldmobile", ["title" => "Greentire Inspection"]); ?>
<style>
	#show_remain {
		position: absolute;
		top: 120px;
		right: 50px;
		font-size: 6em;
		font-weight: bold;
	}
</style>
<div class="head-space"></div>

<div class="panel panel-default form-center">
    <div class="panel-heading"><h1>Quality Check</h1></div>
    <div class="panel-body">
        <form id="formcheckquality">

            <!-- <div class="form-group">
				<label for="gate">Gate</label>
				<select name="gate" id="gate" class="form-control inputs input-lg" required></select>
			</div> -->

            <div class="form-group">
                <label for="barcode">Barcode</label><br>
                <input type="text" name="barcode" id="barcode" class="form-control inputs input-lg" placeholder="Barcode" autocomplete="off">
            </div>

        </form>

    </div>
</div>

<div id="show_remain"></div>

<script>
    jQuery(document).ready(function($) {
        $('#barcode').val('').focus();

        setInterval(function() {
			gojax('get', base_url + '/api/warehouse/total_quality/' + $('input[name=barcode]:checked').val(), )
				.done(function(data) {
					$('#show_remain').text(data.count);
				})
				.fail(function() {
					$('#show_remain').text();
				});
		}, 1000);

        $('#modal_alert').on('hidden.bs.modal', function() {
            $('#barcode').val('').focus();
        });
        
        $('form#formcheckquality').on('submit', function(event) {
            event.preventDefault();
            if (!!$('#barcode').val()) {
                $('#barcode').prop('readonly', false);
                gojax('post', base_url + '/api/quality/check', {
                        // gate: $('#gate').val(),
                        barcode: $('#barcode').val()
                    })
                    .done(function(data) {
                        if (data.status == 200) {
                            //alert($('#barcode').val());
                        } else {
                            alert(data.message);
                        }
                        $('#barcode').prop('readonly', false);
                        $('#barcode').val('').focus();
                    });
            }else {
            alert("กรุณากรอกข้อมูลให้ครบถ้วน");
            $('#barcode').val('').focus();           
            }
        });
    });

    // function getGate() {
    //     gojax('get', base_url + '/api/gate/all')
    //         .done(function(data) {
    //             $('#gate').html('<option value="">= กรุณาเลือก =</option>');
    //             $.each(data, function(index, val) {
    //                 $('#gate').append('<option value="' + val.ID + '">' + val.Description + '</option>')
    //             });
    //         });
    // }

</script>