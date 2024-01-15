<?php $this->layout("layouts/base", ['title' => 'Loading Mobile']); ?>

<style>
  td {
    padding: 10px;
    width: 33.33%;
  }
</style>

<div class="head-space"></div>

<div class="btn-panel" id="panel-scan" style="display: none;">
  <input type="text" id="picking_list_id" class="form-control input-lg" placeholder="Picking List ID" style="width: 300px;">
</div>

<div id="panel_pick_unpick">
  <div class="panel panel-default">
    <div class="panel-heading">Loading</div>
    <div class="panel-body">
      <div class="btn-panel">
        <button class="btn btn-primary btn-lg" id="btn_confirm"> <span class="glyphicon glyphicon-ok"></span> Confirm</button><span style="padding-right: 20px;"></span>
        <button class="btn btn-danger btn-lg" id="btn_cancel"> <span class="glyphicon glyphicon-remove"></span> Cancel</button><span style="padding-right: 20px;"></span>
        <button class="btn btn-info btn-lg" id="btn_pick"> <span class="glyphicon glyphicon-log-in"></span> Pick</button><span style="padding-right: 20px;"></span>
        <button class="btn btn-lg" id="btn_unpick" style="background: orange; color: #ffffff; "> <span class="glyphicon glyphicon-log-out"></span> Unpick</button><span style="padding-right: 20px;"></span>
        <button class="btn btn-lg" id="btn_copy" disabled style="background: blue; color: #ffffff; display: none;"> <span class="glyphicon glyphicon-copy"></span> Copy</button><span style="padding-right: 20px;"></span>
        <button class="btn btn-lg" id="get_data" style="background: green; color: #ffffff; "> <span class="glyphicon glyphicon-download-alt"></span> Get Data</button><span style="padding-right: 20px;"></span>
      </div>

      <div id="grid_pick_unpick"></div>
    </div>
  </div>
</div>

<div id="panel_pick" style="display: none;">
  <div style="margin-bottom: 20px;">
    <table class="table" style="background: #ffffff;">
      <tr>
        <td width="20%">
          Order ID : <input type="text" name="pick_order_id" id="pick_order_id" class="form-control" readonly>
        </td>
        <td width="20%">
          Picking List ID : <input type="text" name="pick_pickinglist_id" id="pick_pickinglist_id" class="form-control" readonly>
        </td>
        <td>
          Customer Name : <input type="text" name="pick_customer" id="pick_customer" class="form-control" readonly>
        </td>
      </tr>
    </table>
    <table class="table">
      <tr>
        <td width="30%"></td>
        <td align="center">
          <input type="text" id="pick_barcode" name="pick_barcode" placeholder="Pick Barcode" class="form-control input-lg">
          <input type="text" id="unpick_barcode" name="unpick_barcode" placeholder="Unpick Barcode" class="form-control input-lg">
        </td>
        <td width="30%">
          <button class="btn btn-primary btn-lg pull-right" id="btn_back" style="margin: 10px 0px;">
            <span class="glyphicon glyphicon-arrow-left"></span> ย้อนกลับ
          </button>
        </td>
      </tr>
    </table>
  </div>

  <div id="grid_pick_loading_line"></div>
</div>

<!--Modal select item to pick-->
<div class="modal" id="modal_select_row_pick" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select Item</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="list_item_line" class="list-group" style="font-weight: bold; font-size: 2em;"></div>
      </div>
    </div>
  </div>
</div>

<!--Modal add remainder-->
<div class="modal" id="modal_add_remainder" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Add Remainder</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div class="alert alert-danger">
          <strong style="font-size: 2em;">Remainder = 0 คุณต้องการเพิ่ม Remainder ?</strong>
        </div>
        <div class="form-group">
          <label for="new_remainder">Remainder</label>
          <input type="number" name="new_remainder" id="new_remainder" class="form-control inputs" required />
        </div>

        <div class="form-group">
          <label for="authorize_code">Authorize Code</label>
          <input type="text" name="authorize_code" class="form-control inputs" id="authorize_code" required>
        </div>

        <div class="form-group">
          <label for="authorize_password">Password</label>
          <input type="password" name="authorize_password" id="authorize_password" class="form-control inputs" required>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Modal force confirm-->
<div class="modal" id="modal_force_confirm" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Confirm Authorize</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->

        <div class="alert alert-danger">

          <strong style="font-size: 2em;">

          </strong>
          <div class="media">
            <div class="media-left">
              <a href="#">
                <img width="100" height="100" src="<?php echo APP_ROOT; ?>/assets/images/error01.png" alt="">
              </a>
            </div>
            <div class="media-body">
              <strong style="font-weight: bold; font-size: 1.5em;">Remainder มากกว่า 0 คุณต้องการ Confirm รายการใช่หรือไม่ ?
                <br /> ระบบจะทำการ Clear Remainder ทั้งหมด = 0</strong>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_authorize_code">Authorize Code</label>
          <input type="text" name="confirm_authorize_code" class="form-control inputs" id="confirm_authorize_code" required>
        </div>

        <div class="form-group">
          <label for="confirm_authorize_password">Password</label>
          <input type="password" name="confirm_authorize_password" id="confirm_authorize_password" class="form-control inputs" required>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Numpad-->
<div class="modal" id="modal_numpad" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">ใส่ Remainder</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="show_number" class="text-center" style="font-size: 2em; font-weight: bold;"></div>
        <table width="100%">
          <tr>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">1</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">2</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">3</button></td>
          </tr>
          <tr>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">4</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">5</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">6</button></td>
          </tr>
          <tr>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">7</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">8</button></td>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">9</button></td>
          </tr>
          <tr>
            <td><button class="btn btn-info btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">0</button></td>
            <td><button class="btn btn-danger btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;">C</button></td>
            <td><button class="btn btn-success btn-block btn-cal" style="height: 50px; font-size: 2em; font-weight: bold;" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-ok"></span></button></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Authorize -->
<div class="modal" id="modal_cancel_authorize" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <span class="glyphicon glyphicon-remove"></span>
          ปิดหน้าต่างนี้
        </button>
        <h4 class="modal-title">Authorize</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div class="form-group">
          <label for="cancel_authorize_code">Authorize Code</label>
          <input type="text" name="cancel_authorize_code" class="form-control inputs" id="cancel_authorize_code" required>
        </div>

        <div class="form-group">
          <label for="cancel_authorize_password">Password</label>
          <input type="password" name="cancel_authorize_password" id="cancel_authorize_password" class="form-control inputs" required>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  jQuery(document).ready(function($) {

    var temp_weight_value = '0';
    var pickState = 'pick';

    // $('#picking_list_id').focus();
    grid_pick_unpick();

    $('#modal_cancel_authorize').on('shown.bs.modal', function() {
      $('#cancel_authorize_code').focus();
    });

    $('#get_data').on('click', function() {
      $('#panel-scan').show();
      $('#panel_pick_unpick').hide();
      $("#picking_list_id").val('').focus();
    });

    $('#new_remainder').focus(function(event) {
      /* Act on the event */
      $('#modal_numpad').modal({
        backdrop: 'static'
      });
    });

    $('.btn-cal').on('click', function() {
      temp_weight_value += $(this).text();
      if ($(this).text() === 'C' || (parseInt(temp_weight_value)) > 99) {
        temp_weight_value = '0';
      }
      $('#show_number').text(parseInt(temp_weight_value));
      $('#new_remainder').val(parseInt(temp_weight_value));
    });

    $('#modal_numpad').on('hidden.bs.modal', function() {
      $('#authorize_code').val('').focus();
    });

    $('#authorize_password').keydown(function(e) {
      if (e.which === 13) {
        var new_remainder = $('#new_remainder');
        var authorize_code = $('#authorize_code');
        var authorize_password = $('#authorize_password');

        var rowdata = row_selected('#grid_pick_loading_line');

        gojax('post', base_url + '/api/loading/add_remainder', {
            new_remainder: new_remainder.val(),
            authorize_code: authorize_code.val(),
            authorize_password: authorize_password.val(),
            inventTransId: rowdata.InventTransId
          })
          .done(function(data) {
            if (data.status === 200) {
              $('#modal_add_remainder').modal('hide');
              $('#grid_pick_loading_line').jqxGrid('updatebounddata');
              $('#pick_barcode').val('').focus();
              // reset
              new_remainder.val('');
              authorize_code.val('');
              authorize_password.val('');
            } else {
              $('#modal_alert').modal({
                backdrop: 'static'
              });
              $('#modal_alert_message').text(data.message);

              new_remainder.val('');
              authorize_code.val('');
              authorize_password.val('');

              onFocus = '#new_remainder';
            }
          });
      }
    });

    $('#confirm_authorize_password').keydown(function(e) {
      if (e.which === 13) {

        var confirm_authorize_code = $('#confirm_authorize_code');
        var confirm_authorize_password = $('#confirm_authorize_password');

        var rowdata = row_selected('#grid_pick_unpick');

        gojax('post', base_url + '/api/loading/force_confirm', {
            code: confirm_authorize_code.val(),
            password: confirm_authorize_password.val(),
            pid: rowdata.PickingListId
          })
          .done(function(data) {

            confirm_authorize_code.val('');
            confirm_authorize_password.val('');

            if (data.status === 200) {
              $('#modal_force_confirm').modal('hide');
              $('#grid_pick_unpick').jqxGrid('updatebounddata');
            } else {
              $('#modal_alert').modal({
                backdrop: 'static'
              });
              $('#modal_alert_message').text(data.message);
              onFocus = '#confirm_authorize_code';
            }


          });
      }
    });

    $('#btn_unpick').on('click', function() {

      var rowdata = row_selected('#grid_pick_unpick');
      pickState = 'unpick';

      $('#grid_pick_loading_line').jqxGrid('clearselection');

      if (typeof rowdata !== 'undefined') {
        if ($.inArray(rowdata.Status, [2, 5]) !== -1) {
          $('#panel_pick_unpick').hide();
          $('#panel_pick').show();

          $('#pick_order_id').val(rowdata.OrderId);
          $('#pick_pickinglist_id').val(rowdata.PickingListId);
          $('#pick_customer').val(rowdata.CustName);

          grid_pick_loading_line(rowdata.PickingListId);

          $('#pick_barcode').hide();
          $('#unpick_barcode').show().val('').focus();
          onFocus = '#unpick_barcode';
        } else {
          $('#modal_alert').modal({
            backdrop: 'static'
          });
          $('#modal_alert_message').text('Status != In-Process or Complete');
        }
      } else {
        $('#modal_alert').modal({
          backdrop: 'static'
        });
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
      }
    });

    $('#unpick_barcode').keydown(function(event) {
      if (event.which === 13) {
        var barcode = $('#unpick_barcode').val();
        var pid = $('#pick_pickinglist_id').val();
        var rowdata = row_selected('#grid_pick_loading_line');
        // console.log(rowdata);
        if (typeof rowdata !== 'undefined') {
          if ($.trim(barcode) !== '') {

            $('#unpick_barcode').prop('readonly', true);

            gojax('post', base_url + '/api/loading/unpick/save', {
                barcode: barcode,
                pid: pid,
                inventTransId: rowdata.InventTransId,
                LineID: rowdata.LineID
              })
              .done(function(data) {
                var temp_barcode = data.message;
                if (data.status === 200) {
                  // $('#top_alert').show();
                  // $('#top_alert_message').text('Barcode ล่าสุด ' + barcode);
                  $('#top_alert').show();
                  $('#top_alert_message').text(data.curecode + ', ' + data.batch + ' => ' + temp_barcode);
                  $('#modal_alert').modal('hide');
                  $('#grid_pick_loading_line').jqxGrid('updatebounddata', 'cells');
                } else {
                  $('#top_alert').hide();
                  $('#modal_alert').modal({
                    backdrop: 'static'
                  });
                  $('#modal_alert_message').text(data.message);
                  onFocus = '#unpick_barcode';
                }

                setTimeout(function() {
                  $('#unpick_barcode').prop('readonly', false);
                  $('#unpick_barcode').val('').focus();
                }, 1000);
              });
          }
        } else {
          $('#modal_alert').modal({
            backdrop: 'static'
          });
          $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
          $('#pick_barcode').val('').focus();
        }

      }
    });

    $('#btn_confirm').on('click', function() {
      var rowdata = row_selected('#grid_pick_unpick');
      var isCustomRemainder = false;
      if (typeof rowdata !== 'undefined') {
        if (rowdata.Status === 5) {
          $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 600,
            modal: true,
            buttons: {
              "Yes": function() {
                close_button(); // close button
                gojax('post', base_url + '/api/loading/is_custome_remainder', {
                    pid: rowdata.PickingListId
                  })
                  .done(function(data) {
                    if (data.status === 200) {
                      isCustomRemainder = false;
                    } else {
                      isCustomRemainder = true;
                    }

                    gojax('post', base_url + '/api/loading/confirm', {
                        pid: rowdata.PickingListId,
                        isCustomRemainder: isCustomRemainder
                      })
                      .done(function(data) {
                        if (data.status === 200) {
                          $('#grid_pick_unpick').jqxGrid('updatebounddata');
                        } else {
                          $('#modal_alert').modal({
                            backdrop: 'static'
                          });
                          $('#modal_alert_message').text(data.message);
                        }
                      });
                  });
                open_button(); // open button
                $(this).dialog("close");
              },
              Cancel: function() {
                $(this).dialog("close");
              }
            }
          });
        } else {
          // $('#modal_alert').modal({backdrop: 'static'});
          // $('#modal_alert_message').text('Status != Complete');

          // Step add remainder
          $('#modal_force_confirm').modal({
            backdrop: 'static'
          });
          $('#confirm_authorize_code').val('').focus();
          // End step add remainder
        }
      } else {
        $('#modal_alert').modal({
          backdrop: 'static'
        });
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
      }
    });

    $('#modal_alert').on('click', function() {
      $(onFocus).val('').focus();
    });

    $('#btn_back').on('click', function() {
      $('#panel_pick').hide();
      $('#panel_pick_unpick').show();
      $('#grid_pick_unpick').jqxGrid('updatebounddata');
      $('#grid_pick_unpick').jqxGrid('clearselection');
    });

    $('#cancel_authorize_password').keydown(function(event) {
      if (event.which === 13) {
        var rowdata = row_selected('#grid_pick_unpick');
        if (typeof rowdata !== 'undefined') {
          if ($.inArray(rowdata.Status, [1, 2, 5]) != -1) {
            close_button(); // close button
            // post data cancel to server
            gojax('post', base_url + '/api/loading/cancel', {
                pid: rowdata.PickingListId,
                user: $('#cancel_authorize_code').val(),
                pass: $('#cancel_authorize_password').val(),
                type: 'Loading'
              })
              .done(function(data) {
                if (data.status === 200) {
                  $('#grid_pick_unpick').jqxGrid('updatebounddata');
                  $('#modal_cancel_authorize').modal('hide');
                } else {
                  $('#modal_alert').modal({
                    backdrop: 'static'
                  });
                  $('#modal_alert_message').text(data.message);
                  $('#cancel_authorize_code').val('');
                  $('#cancel_authorize_password').val('');
                  onFocus = '#cancel_authorize_code';
                }
              });

            open_button(); // open button
          } else {
            $('#modal_alert').modal({
              backdrop: 'static'
            });
            $('#modal_alert_message').text('Status != Open, In-Progess, Complete');
          }
        } else {
          $('#modal_alert').modal({
            backdrop: 'static'
          });
          $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
        }
      }
    });

    $('#btn_cancel').on('click', function() {
      $('#modal_cancel_authorize').modal({
        backdrop: 'static'
      });
      $('#cancel_authorize_password').val('');
      $('#cancel_authorize_code').val('').focus();
    });

    $('#pick_barcode').keydown(function(event) {
      if (event.which === 13) {
        var barcode = $('#pick_barcode').val();
        var pid = $('#pick_pickinglist_id').val();
        var rowdata = row_selected('#grid_pick_loading_line');
        // console.log(rowdata);
        if (typeof rowdata !== 'undefined') {
          if ($.trim(barcode) !== '') {

            $('#pick_barcode').prop('readonly', true);

            gojax('post', base_url + '/api/loading/pick/save', {
                barcode: barcode,
                pid: pid,
                inventTransId: rowdata.InventTransId
              })
              .done(function(data) {
                // console.log(data);
                var temp_barcode = data.message;
                if (data.status === 200) {
                  // $('#top_alert').show();
                  // $('#top_alert_message').text('Barcode ล่าสุด ' + barcode);
                  $('#top_alert').show();
                  $('#top_alert_message').text(data.curecode + ', ' + data.batch + ' => ' + temp_barcode);
                  $('#modal_alert').modal('hide');
                  $('#grid_pick_loading_line').jqxGrid('updatebounddata', 'cells'); // golf 20201017
                } else if (data.status === 901) { // remainder = 0

                  // Step add remainder
                  $('#modal_add_remainder').modal({
                    backdrop: 'static'
                  });
                  // End step add remainder
                } else if (data.status === 405) {
                  $('#modal_warning').modal({
                    backdrop: 'static'
                  });
                  $('#modal_warning_message').text(data.message);
                } else {
                  $('#top_alert').hide();
                  $('#modal_alert').modal({
                    backdrop: 'static'
                  });
                  $('#modal_alert_message').text(data.message);
                  onFocus = '#pick_barcode';
                }

                setTimeout(function() {
                  $('#pick_barcode').prop('readonly', false);
                  $('#pick_barcode').val('').focus();
                }, 1000);

              });
          }
        } else {
          $('#modal_alert').modal({
            backdrop: 'static'
          });
          $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
          $('#pick_barcode').val('').focus();
        }

      }
    });

    $('#modal_add_remainder').on('shown.bs.modal', function() {
      $('#new_remainder').val('').mask('00');
    });

    $('#btn_pick').on('click', function() {

      var rowdata = row_selected('#grid_pick_unpick');
      pickState = 'pick';

      $('#grid_pick_loading_line').jqxGrid('clearselection');

      if (typeof rowdata !== 'undefined') {
        $('#panel_pick_unpick').hide();
        $('#panel_pick').show();

        $('#pick_order_id').val(rowdata.OrderId);
        $('#pick_pickinglist_id').val(rowdata.PickingListId);
        $('#pick_customer').val(rowdata.CustName);

        grid_pick_loading_line(rowdata.PickingListId);

        $('#unpick_barcode').hide();
        $('#pick_barcode').show().val('').focus();
        onFocus = '#pick_barcode';
      } else {
        $('#modal_alert').modal({
          backdrop: 'static'
        });
        $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
      }
    });

    // $('#modal_select_row_pick').on('hidden.bs.modal', function() {
    //   $('#grid_pick_loading_line').jqxGrid('clearselection');
    // });

    $('#grid_pick_unpick').on('rowselect', function(event) {
      var args = event.args;
      var rowBoundIndex = args.rowindex;
      var rowdata = args.row;
      if (rowdata.Status === 1) { // open
        open_button();
        $('#btn_confirm').prop('disabled', true);
        $('#btn_unpick').prop('disabled', true);
      } else if (rowdata.Status === 2) { // in-progress
        open_button();
        $('#btn_copy').prop('disabled', true);
      } else if (rowdata.Status === 3) { // confirm
        close_button();
      } else if (rowdata.Status === 5) { // conplete
        open_button();
        $('#btn_copy').prop('disabled', true);
      } else {
        open_button();
      }
    });

    $('#grid_pick_loading_line').on('rowselect', function() {

      $('#modal_select_row_pick').modal({
        backdrop: 'static'
      });

      var rowdata = $('#grid_pick_loading_line').jqxGrid('getrows');
      //console.log(rowdata);

      $('#list_item_line').html('');

      $.each(rowdata, function(index, el) {
        if (pickState === 'pick') {
          $('#list_item_line').append('<a href="#" onClick="return setRowSelect(' + el.uid + ',' + el.Remainder + ')" class="list-group-item">' + el.ItemId + ' (จำนวน ' + el.Remainder + ')</a>');
        } else if (pickState === 'unpick') {
          $('#list_item_line').append('<a href="#" onClick="return setRowSelect(' + el.uid + ',1)" class="list-group-item">' + el.ItemId + ' (จำนวน ' + el.LoadingQTY + ')</a>');
        }
      });

    });

    $('#picking_list_id').keydown(function(event) {
      var pid = $('#picking_list_id').val();
      if (event.which === 13) {
        gojax('get', base_url + '/api/loading/table/' + pid + '/create')
          .done(function(data) {
            if (data.status === 200) {
              $('#panel_pick_unpick').show();
              $('#panel-scan').hide();
              grid_pick_unpick(data.pid);
            } else {
              $('#modal_alert').modal({
                backdrop: 'static'
              });
              $('#modal_alert_message').text(data.message);
              onFocus = '#picking_list_id';
            }
          });
      }
    });
  });

  function setRowSelect(id, qty) {

    // console.log(id+'....'+qty);
    $('#grid_pick_loading_line').jqxGrid('selectrow', id);
    $('#modal_select_row_pick').modal('hide');
    $(onFocus).val('').focus();
    // // alert(qty + typeof(qty));
    if (qty === 0) {
      $('#modal_add_remainder').modal({
        backdrop: 'static'
      });
      $('#new_remainder').focus();
    }
  }

  function grid_pick_loading_line(pid) {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'LineID',
          type: 'string'
        },
        {
          name: 'InventTransId',
          type: 'string'
        },
        {
          name: 'ItemId',
          type: 'string'
        },
        {
          name: 'Name',
          type: 'string'
        },
        {
          name: 'OrderQty',
          type: 'number'
        },
        {
          name: 'OrderUnit',
          type: 'number'
        },
        {
          name: 'Remainder',
          type: 'number'
        },
        {
          name: 'LoadingQTY',
          type: 'number'
        },
        {
          name: 'Status',
          type: 'number'
        },
        {
          name: 'StatusDesc',
          type: 'string'
        }
      ],
      url: base_url + '/api/loading/line/' + pid
    });

    return $("#grid_pick_loading_line").jqxGrid({
      width: '100%',
      source: dataAdapter,
      autoheight: true,
      // pageSize : 20,
      altrows: true,
      // pageable : true,
      sortable: true,
      columnsresize: true,
      rowsheight: 60,
      theme: 'theme',
      columns: [
        // { text: 'Line ID', datafield: 'LineID', width: 100},
        {
          text: 'Item ID',
          datafield: 'ItemId',
          width: 80
        },
        {
          text: 'Name',
          datafield: 'Name',
          width: 350
        },
        {
          text: 'Order QTY',
          datafield: 'OrderQty',
          width: 80
        },

        {
          text: 'Remainder',
          datafield: 'Remainder',
          width: 80
        },
        {
          text: 'Loading QTY',
          datafield: 'LoadingQTY',
          width: 90
        },
        {
          text: 'Order Unit',
          datafield: 'OrderUnit',
          width: 80
        },
        {
          text: 'Status',
          datafield: 'StatusDesc',
          width: 100,
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            var cl;

            if (rowdata.Status == 1) {
              cl = 'white';
            } else if (rowdata.Status == 2) {
              cl = 'orange';
            } else if (rowdata.Status == 5) {
              cl = 'green';
            }
            return '<div style=\'padding: 15px; height: 60px; background : ' + cl + ' ; color:#000000;\'> ' + value + ' </div>';
          }
        }
      ]
    });
  }

  function grid_pick_unpick() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'DocNo',
          type: 'string'
        },
        {
          name: 'OrderId',
          type: 'string'
        },
        {
          name: 'PickingListId',
          type: 'string'
        },
        {
          name: 'DeliveryDate',
          type: 'date'
        },
        {
          name: 'ConfirmDate',
          type: 'date'
        },
        {
          name: 'CustName',
          type: 'string'
        },
        {
          name: 'Status',
          type: 'number'
        },
        {
          name: 'StatusDesc',
          type: 'string'
        },
        {
          name: 'CreatedDate',
          type: 'date'
        }
      ],
      url: base_url + "/api/loading/table/all"
    });
    return $("#grid_pick_unpick").jqxGrid({
      width: '100%',
      source: dataAdapter,
      autoheight: true,
      pageSize: 20,
      rowsheight: 50,
      // columnsheight : 40,
      altrows: true,
      pageable: true,
      // sortable: true,
      filterable: true,
      showfilterrow: true,
      columnsresize: true,
      theme: 'theme',
      columns: [
        // { text: 'Doc No.', datafield: 'DocNo', width: 250},
        {
          text: 'Order ID',
          datafield: 'OrderId',
          width: 100
        },
        {
          text: 'Picking List ID',
          datafield: 'PickingListId',
          width: 100
        },
        {
          text: 'Customer Name',
          datafield: 'CustName',
          width: 320
        },
        {
          text: 'Delivery Date',
          datafield: 'CreatedDate',
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd',
          width: 100
        },
        {
          text: 'Comfirm Date',
          datafield: 'ConfirmDate',
          width: 100,
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd'
        },
        {
          text: 'Status',
          datafield: 'StatusDesc',
          width: 100,
          filtertype: 'checkedlist',
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            var cl;

            if (rowdata.Status == 1) {
              cl = 'white';
            } else if (rowdata.Status == 2) {
              cl = 'orange';
            } else if (rowdata.Status == 3) {
              cl = 'red';
            } else if (rowdata.Status == 5) {
              cl = 'green';
            }
            return '<div style=\'padding: 15px; height: 60px; background : ' + cl + ' ; color:#000000;\'> ' + value + ' </div>';
          }
        }
      ]
    });
  }
</script>