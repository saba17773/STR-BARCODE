<?php $this->layout("layouts/base", ['title' => 'Transfer LPN']); ?>

<h1 class="text-center">Transfer LPN</h1>
<hr>

<div style="margin: auto; max-width: 400px;">
  <label for="">To LPN No.</label> <br>
  <input type="text" name="lpn" class="form-control inputs"> <br>
  <label for="">Barcode</label> <br>
  <input type="text" name="barcode" class="form-control inputs">
</div>


<script>
  jQuery(document).ready(function($) {
    $('input[name=lpn]').val('').focus();

    $('input[name=barcode]').keydown(function(e) {
      if (e.which === 13) {
        gojax('post', '/api/v2/transfer_lpn', {
          lpn: $('input[name=lpn]').val(),
          barcode: $('input[name=barcode]').val()
        }).done(function(data) {

          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Transfer From LPN : ' + $('input[name=lpn]').val() + 'To LPN : '+ data.from_lpn +' Success.' );
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
          }

          $('input[name=barcode]').val('');
          $('input[name=lpn]').val('').focus();
          // alert(data.message);
        });
      }
    });
  });
</script>