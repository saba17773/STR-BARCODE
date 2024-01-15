<?php $this->layout("layouts/base", ['title' => 'STR TO SVO WH']); ?>

<style>
  #show_count_by_journal {
    font-size: 2em;
    font-weight: bold;
  }
</style>

<h1 style="text-align: center;">STR TO SVO WH</h1>

<hr>

<div style="width: 300px; margin: 0 auto;">
  <div class="form-group">
    <label for="journal">Journal</label>
    <select name="journal" id="journal" class="form-control" required></select>
    <span id="show_count_by_journal">จำนวน 0</span>
  </div>
  <div class="form-group">
    <label for="barcode">Barcode</label>
    <input type="text" name="barcode" class="form-control inputs" autocomplete="off">
  </div>
</div>


<script>
  function loadTotalScan() {
    gojax('post', '/api/v1/journal_count_by_journal', {
      journal_id: $('#journal').val()
    }).done(function (data) {
      $('#show_count_by_journal').html('จำนวน ' + data.count);
    });
  }

  jQuery(document).ready(function ($) {
    $('input[name=barcode]')
      .val('')
      .focus();


    $("#journal").on("change", function () {
      loadTotalScan();
    });

    // setInterval(function () {
    //   gojax('post', '/api/v1/journal_count_by_journal', {
    //     journal_id: $('#journal').val()
    //   }).done(function (data) {
    //     $('#show_count_by_journal').html('จำนวน ' + data.count);
    //   });
    // }, 5000);

    $('#modal_alert').on('hidden.bs.modal', function () {
      $('input[name=barcode]').val('').focus();
    });

    $('input[name=barcode]').on('keydown', function (e) {
      if (e.which === 13 && $.trim($("#journal").val()) === ''){
        alert('กรุณาเลือก Journal');
      } else if (e.which === 13) {
        gojax('post', '/transfer/str_to_svo_wh/save', {
          barcode: $('input[name=barcode]').val(),
          journal: $("#journal").val()
        }).done(function (data) {
          if (data.result === true) {
            $('#top_alert').show();
            $('#top_alert_message').text(data.curecode + ', '+ data.batch +' => ' + $('input[name=barcode]').val());
            $('#modal_alert').modal('hide');
            loadTotalScan();
          } else {
            $('#top_alert').hide();
            $('#modal_alert').modal({ backdrop: 'static' });
            $('#modal_alert_message').text(data.message);
          }
          
          $('input[name=barcode]').val('').focus();
        });
      }
    });

    $('#journal').on('select2:select', function () {
      $('input[name=barcode]').val('').focus();
    });

    gojax('get', '/api/v1/journal_no_complete')
      .done(function (data) {

        $('#journal').html("<option value=''>= เลือก =</option>");

        $.each(data, function (i, v) {
          $('#journal').append("<option value='" + v.JournalID + "'>" + v.JournalID + " (" + v.Plate + ")</option>");
        });

        $('#journal').select2();
      });


  });
</script>