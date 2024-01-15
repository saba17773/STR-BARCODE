<?php $this->layout("layouts/base", ['title' => 'Transfer Location']); ?>

<div style="margin: auto; max-width: 400px;">
  <h1>Transfer Location</h1>
  <hr>

  <form id="formPalletTransfer">
    <div class="form-group">
      <label for="pallet_no">Pallet No.</label>
      <input type="text" id="pallet_no" name="pallet_no" class="form-control inputs" autofocus>
    </div>

    <div class="form-group">
      <label for="location">Location</label>
      <input type="text" id="location" name="location" class="form-control inputs">
    </div>
  </form>
</div>

<div id="show_result" 
style="padding: 10px; 
    text-align: center;
    max-width: 300px;
    margin: 20px auto;
    border: 1px solid;"> กรุณายิง Barcode </div>

<script>
  jQuery(document).ready(function ($) {

    $('#modal_alert').on('hidden.bs.modal', function() {
      $('input[name=location]').val('');
      $('input[name=pallet_no]').val('').focus();
    });
    // 
    $('input[name=location]').keydown(function (e) {
      if (e.which === 13) {
        gojax('post', '/api/v1/wh_location/transfer_location/save', {
          pallet_no: $('input[name=pallet_no]').val(),
          location: $('input[name=location]').val()
        }).done(function (data) {
          // alert(data.message);
          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Location ที่ได้ ' + data.extra.new_location);
            $('#modal_alert').modal('hide');
            $('#show_result').text(data.extra.old_location + ' =====> ' + data.extra.new_location);
            $('input[name=location]').val('');
            $('input[name=pallet_no]').val('').focus();
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
            $('#show_result').text('...');
          }
        });
      }
    });
  });
</script>

