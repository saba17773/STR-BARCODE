<?php $this->layout("layouts/base", ['title' => 'Force SVO']); ?>

<h1 style="text-align: center;">Force SVO</h1>

<hr>

<div style="width: 300px; margin: 0 auto;">
  <div class="form-group">
    <label for="barcode">Barcode</label>
    <input type="text" name="barcode" class="form-control inputs">
  </div>
</div>


<script>
  jQuery(document).ready(function ($) {
    $('input[name=barcode]')
      .val('')
      .focus();

    $('#modal_alert').on('hidden.bs.modal', function () {
      $('input[name=barcode]').val('').focus();
    });

    $('input[name=barcode]').on('keydown', function (e) {
      if (e.which === 13) {
        gojax('post', '/save_force_svo', {
          barcode: $('input[name=barcode]').val()
        }).done(function (data) {
          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text('Barcode ล่าสุด ' + $('input[name=barcode]').val());
            $('#modal_alert').modal('hide');
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({ backdrop: 'static' });
            $('#modal_alert_message').text(data.message);
          }
          $('input[name=barcode]').val('').focus();
        });
      }
    });
  });
</script>