<?php $this->layout("layouts/base", ['title' => 'WMS Ship Detail']); ?>
<!-- <h1 class="head-text">WMS Ship Detail</h1> -->
<!-- <hr> -->
<br>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
    <div class="panel-heading">WMS Ship Detail Report</div>
	<div class="panel-body">
		<form id="form_shipdetail" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/shipdetail" target="_blank">

			<div class="form-group">
				<label for="LOADID">LOADID</label>
                <select name="selectMenu[]" id="selectMenu" style="width: 370px" class="form-control select2" required>
				</select>
			</div>

            <div class="form-group">
				<label for="EXTERNORDERKEY">EXTERNORDERKEY</label>
                <select name="selectMenuData[]" id="selectMenuData" style="width: 370px" multiple="multiple" required>
				</select>
			</div>

			<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
			<!-- <button type="button" id="btn_test" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> test</button> -->

		</form>
	</div>
</div>

<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" /> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->

<script type="text/javascript">
	jQuery(document).ready(function($) {
        $(".select2").select2();
		$('#selectMenu').html("");
        $('#selectMenuData').html("");
        $('#selectMenuData').multipleSelect();
		getPressLoadid()
			.done(function(data) {
                console.log(data);
                $('#selectMenu').append('<option value="">    </option>');
                // $('#selectMenuData').append('<option value="">Please select</option>');
				$.each(data, function(k, v) {
					$('#selectMenu').append('<option value="' + v.LOADID + '">' + v.LOADID + '</option>');
				});
				// $('#selectMenu').multipleSelect({
				// 	single: true
				// });
			});	
       
        $(".select2").on('change',function () { 
            let dataload = $('#selectMenu').val();
            console.log(dataload);
            $('#selectMenuData').html(""); 
            getPressExternorderkey(dataload);
        });

	});

	function getPressLoadid() {
		return $.ajax({
			url: base_url + '/api/press/loadid',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}

    function getPressExternorderkey(dataload) {
		return $.ajax({
			url: base_url + '/api/press/externorderkey',
			type: 'get',
			dataType: 'json',
			cache: false,
            data: {
                dataload: dataload
            },
            success:function(data) {
                console.log(data);
                // $('#selectMenuData').append('<option value="">Please select</option>');
                $.each(data, function(k, v) {
                    $('#selectMenuData').append('<option value="' + v.EXTERNORDERKEY + '">' + v.EXTERNORDERKEY + '</option>');
                });
                $('#selectMenuData').multipleSelect({
                    single: false
                });
            }
		});
	}
</script>