<?php $this->layout("layouts/base", ['title' => 'GreenTire Inventory']); ?>
<h1 class="head-text">GreenTire Inventory Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/inventory">

      <div class="form-group">
              <label for="BOI">BOI</label><br>
              <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple"   style="width: 370px" >
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

		<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function($) {
  $( "#date_building" ).datepicker({dateFormat: 'dd-mm-yy'});
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
