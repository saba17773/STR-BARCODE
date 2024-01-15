<?php $this->layout("layouts/base", ['title' => 'Pallet Receive']); ?>

<h1 class="text-center">รับยางเข้า Pallet</h1>

<div style="margin: auto; max-width: 300px;">
  <form id="formPalletReceive">
    <label for="">Pallet No.</label> <br>
    <input type="text" name="palletNo" class="form-control inputs" required> <br>
    <label for="">Barcode</label> <br>
    <input type="text" name="barcode" class="form-control inputs" required> <br>
    <button type="button" id="complete" class="btn btn-primary">Complete</button>
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
    $('input[name=palletNo]').val('').focus();

    $('#modal_alert').on('hidden.bs.modal', function() {
      $('input[name=barcode]').val('');
      $('input[name=palletNo]').val('').focus();
    });

    $('input[name=barcode]').keydown(function (e) {
      if (e.which === 13) {
        $('#complete').prop('disabled', true);

        var _p = $('input[name=palletNo]').val();
        var _b = $('input[name=barcode]').val();

        gojax('post', '/api/v1/wh_location/pallet_receive/save', {
          pallet_no: $('input[name=palletNo]').val(),
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

          $('form#formPalletReceive').trigger('reset');
          $('input[name=palletNo]').focus();
          $('#complete').prop('disabled', false);

          $('#show_result').html(_b + ' =====> ' + _p);
        });
      }
    });

    $('#complete').on('click', function () {
      if (confirm('คุณยืนยันจะ Complete Pallet นี้ใช่หรือไม่?')) {
        gojax('post', '/api/v1/wh_location/pallet_complete', {
          pallet_no: $('input[name=palletNo]').val()
        }).done(function (data) {

          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Location ที่ได้ ' + data.extra.location);
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
          }

          $('form#formPalletReceive').trigger('reset');
          $('input[name=palletNo]').focus();
          $('#complete').prop('disabled', false);
          $('#show_result').html('');
        });
      }
    });
  });
</script>