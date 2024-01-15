<?php $this->layout("layouts/base", ['title' => 'Pallet Transfer']); ?>

<div style="margin: auto; max-width: 400px;">
  <h1>Pallet Transfer</h1>
  <hr>

  <form id="formPalletTransfer">
    <div class="form-group">
      <label for="pallet_no">Pallet No.</label>
      <input type="text" id="pallet_no" name="pallet_no" class="form-control inputs" autofocus>
    </div>

    <div class="form-group">
      <label for="barcode">Barcode</label>
      <input type="text" id="barcode" name="barcode" class="form-control inputs">
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
      $('input[name=barcode]').val('');
      $('input[name=pallet_no]').val('').focus();
    });

    // 
    $('input[name=barcode]').keydown(function (e) {
      if (e.which === 13) {
        // $('#barcode').prop('disabled', true);
        gojax('post', '/api/v1/wh_location/pallet_transfer/save', {
          pallet_no: $('input[name=pallet_no]').val(),
          barcode: $('input[name=barcode]').val()
        }).done(function (data) {
          
          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text(data.extra.curing_code +', '+data.extra.batch+', '+data.extra.barcode);
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
          }

          $('#barcode').prop('disabled', false);
          $('#formPalletTransfer').trigger('reset');
          $('input[name=pallet_no]').val('').focus();

          $('#show_result').text(data.extra.old_location + ' =====> ' + data.extra.new_location);

        });
      }
    });
  });
</script>