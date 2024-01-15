<?php $this->layout("layouts/base", ['title' => 'Transfer Location']); ?>

<h1 class="text-center">Transfer Location</h1>
<hr>



<div style="margin: auto; max-width: 400px;">
  <label for="">LPN No.</label> <br>
  <input type="text" name="lpn" class="form-control inputs"> <br>
  <label for="">Location</label> <br>
  <input type="text" name="location" class="form-control inputs">
</div>

<!-- <button class=" btn btn-info">Test ปุ่ม ไม่มีอะไรจ้า</button> -->

<script>
  jQuery(document).ready(function($) {

    // $('.btn')
    //   .removeClass('btn-info')
    //   .addClass('btn-primary');

    $('input[name=lpn]').val('').focus();

    $('input[name=location]').keydown(function(e) {
      if (e.which === 13) {
        $('#http-loading').show();
        gojax('post', '/api/v2/transfer_location', {
          lpn: $('input[name=lpn]').val(),
          location: $('input[name=location]').val()
        }).done(function(data) {
          $('#http-loading').hide();

          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Transfer To Location : ' + data.to_location + ' Success.' );
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
          }

          $('input[name=location]').val('');
          $('input[name=lpn]').val('').focus();
          // alert(data.message);
        });
      }
    });
  });
</script>