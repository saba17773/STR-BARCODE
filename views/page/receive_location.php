<?php $this->layout("layouts/base", ['title' => 'Receive Location']); ?>

<h1 class="text-center">Receive Location</h1>
<hr>

<div style="margin: auto; max-width: 400px;">
  <form id="form_receive_location">
    <label for="">LPN No.</label> <br>
    <input type="text" name="lpn" class="form-control inputs" required> <br>
    <label for="">Barcode</label> <br>
    <input type="text" name="barcode" class="form-control inputs" required> <br>
    <button type="button" id="complete" class="btn btn-primary">Complete</button>
  </form>
</div>

<script>
  jQuery(document).ready(function($) {
    $('input[name=lpn]').val('').focus();

    $('input[name=barcode]').keydown(function(e) {
      if (e.which === 13) {
        gojax('post', '/api/v2/receive_location', {
          lpn: $('input[name=lpn]').val(),
          barcode:  $('input[name=barcode]').val()
        }).done(function(data) {
          // $('input[name=barcode]').val('');
          // $('input[name=lpn]').val('').focus();

          if (data.result === true) {
            $('#top_alert').show();

            if ( data.location !== null && data.location !== '') {
              $('#top_alert_message').text('Complete LPN Location [' + data.location + '] Success.' );
            } else {
              $('#top_alert_message').text('Barcode ล่าสุด ' + $('input[name=barcode]').val() );
            }
            
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

    $('#complete').on('click', function() {
      gojax('post', '/api/v2/receive_location/complete', {
        lpn: $('input[name=lpn]').val(),
        barcode:  $('input[name=barcode]').val()
      }).done(function(data) {

        if (data.result === true) {
          $('#top_alert').show();
          $('#top_alert_message').text('Complete LPN Location [' + data.location + '] Success.' );
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
    });
  });
</script>