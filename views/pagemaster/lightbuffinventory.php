<?php $this->layout("layouts/base", ['title' => 'Light Buff Inventory']); ?>
<style>
  .btn-xl {
    padding: 5px 30px;
    font-size: 20px;
    border-radius: 5px;
  }
</style>
<h1 class="head-text">Light Buff Inventory</h1>
<hr>
<div class="panel panel-default form-center">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/lightbuffinventory" target="_blank">
      <div class="form-group">
        <label for="BOI">BOI</label><br>
        <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" style="width: 400px">
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

      <!-- <button type="submit" class="btn btn-primary btn-lg btn-block">Print</button> -->
      <input type="hidden" value="" name="check_type" id="check_type" />
      <button type="button" class="btn btn-primary btn-xl " id="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
      <button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>

    </form>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {
    //  $( "#date_internal" ).datepicker({dateFormat: 'dd-mm-yy'});
    $('#selectMenuBOI').html("");
    $('#selectMenuBOI').multipleSelect({
      single: true
    });
    getPressSideBOI()
      .done(function(data) {
        $('#selectMenuBOI').append('<option value="1">ALL</option>');
        $.each(data, function(k, v) {
          $('#selectMenuBOI').append('<option value="' + v.ID + '">' + v.ID + '</option>');
        });
        $('#selectMenuBOI').multipleSelect({
          single: true
        });
      });

    $('#to_pdf').on('click', function(event) {
      event.preventDefault();
      $('#to_excel').attr("disabled", true);
      setTimeout(function() {
        $('#to_excel').attr("disabled", false);
      }, 10000);
      $('input[name=check_type]').val(1);
      $('#form_internal').submit();

    });

    $('#to_excel').on('click', function(event) {
      event.preventDefault();
      $('#to_pdf').attr("disabled", true);
      setTimeout(function() {
        $('#to_pdf').attr("disabled", false);
      }, 10000);
      $('input[name=check_type]').val(2);
      $('#form_internal').submit();
    });
  });

  function getPressSideBOI() {
    return $.ajax({
      url: base_url + '/api/press/allBOI',
      type: 'get',
      dataType: 'json',
      cache: false
    });
  }
</script>