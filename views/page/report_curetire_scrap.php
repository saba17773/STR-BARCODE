<?php $this->layout("layouts/base", ["title" => "Report Curetire Scrap"]) ?>
<style>
.btn-xl {
    padding: 5px 45px;
    font-size: 20px;
    border-radius: 5px;
}

</style>
<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Curetire Scrap</div>
	<div class="panel-body">
		<form id="formDateScrap" method="post" action="<?php echo APP_ROOT; ?>/report/curetire/scrap_report" target="_blank">
			<div class="form-group">
				<label for="date_scrap">Date Scrap</label>
				<input type="date" name="date_scrap" id="date_scrap" class="form-control">
        <input type="hidden" name="checkdata" id="checkdata" value="">
			</div>
      <div class="form-group">
              <label for="BOI">BOI</label><br>
              <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple"   style="width: 472px" >
              </select>
      </div>
			<div class="form-group" style="display: block;">
				<strong>Type : </strong>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="tbr" /> TBR
				</label>
				<label style="padding-left: 40px;">
					<input type="radio" name="item_group" value="pcr" /> PCR
				</label>
			</div>
			<button type="button" class="btn btn-primary btn-xl " id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
			<button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#date_scrap').datepicker({
			dateFormat: 'yy-mm-dd',
			'setDate': new Date()
		});

    $('#selectMenuBOI').html("");
    $('#selectMenuBOI').multipleSelect({single: true});
    getPressSideBOI()
        .done(function(data) {
            $('#selectMenuBOI').append('<option value="1">ALL</option>');
          $.each(data, function(k, v) {
            $('#selectMenuBOI').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
          });
            $('#selectMenuBOI').multipleSelect({single: true});
        });

		// $('#formDateScrap').submit(function(event) {
		// 	event.preventDefault();
		// 	var date_scrap = $('#date_scrap').val();
		// 	var product_group = $('input[name=item_group]:checked').val();
		// 	if (!!date_scrap) {
		// 		// console.log(date_scrap);
		// 		window.open(base_url+'/report/curetire/scrap/'+date_scrap+'/'+product_group, '_blank');
		// 	}
		// });

		$('#to_pdf').on('click', function(event) {
			event.preventDefault();
			$('#to_excel').attr("disabled", true);
			setTimeout(function () {
			$('#to_excel').attr("disabled", false);
			}, 10000);
			var date_scrap = $('#date_scrap').val();
			var product_group = $('input[name=item_group]:checked').val();
		    $('input[name=checkdata]').val(1);
      if (!!date_scrap) {
       $('#formDateScrap').submit();
			//$('#formDateScrap').submit(window.open(base_url+'/report/curetire/scrap/'+date_scrap+'/'+product_group+'/'+check, '_blank'));

      }
		});

		$('#to_excel').on('click', function(event) {
					event.preventDefault();
					$('#to_pdf').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_pdf').attr("disabled", false);
	        }, 10000);
					var date_scrap = $('#date_scrap').val();
					var product_group = $('input[name=item_group]:checked').val();
				 $('input[name=checkdata]').val(2);
					if (!!date_scrap) {
            $('#formDateScrap').submit();
			     //('#formDateScrap').submit(window.open(base_url+'/report/curetire/scrap/'+date_scrap+'/'+product_group+'/'+check, '_blank'));

          }
				});
	});
  function getPressSideBOI() {
    return $.ajax({
      url : base_url + '/api/press/allBOI',
      type : 'get',
      dataType : 'json',
      cache : false
    });
  }

</script>
