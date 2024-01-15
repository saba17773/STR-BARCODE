<?php $this->layout("layouts/base", ['title' => 'New Movement Issue']); ?>

<style>

</style>

<div class="row">
  <div class="col-md-6 col-sm-6">

    <h3 class="head-text">Create new Movement Issue</h3>

    <div class="panel panel-defeult" id="panel-new">
      <div class="panel-body">
        <form id="formNewIssue">
          <div class="form-group">
            <label for="employee_code">Employee Code</label>
            <select name="employee_code" id="employee_code" class="form-control input-lg"  required></select>
          </div>
          <div class="form-group">
            <label for="division">Department</label>
            <input type="text" name="division" id="division" class="form-control input-lg" readonly required>
            <input type="hidden" name="division_value" id="division_value" class="form-control input-lg" readonly required>
          </div>
          <div class="form-group">
            <label for="usercheck">USER:</label>
            <input type="text" name="user" id="user" class="form-control input-lg" >
          </div>
          <div class="form-group">
            <label for="passwordcheck">Password:</label>
            <input type="password" name="pass" id="pass" class="form-control input-lg" >
          </div>

          <!-- <button class="btn btn-primary btn-lg btn-block" type="submit" hidden ="hidden">Save</button> -->
            <button  type="submit" hidden ="hidden"></button>
        </form>
      </div>
    </div>

    <div class="panel panel-default hide" id="panel-barcode">
      <div class="panel-body">
        <form id="formBarcode">
          <div class="form-group">
            <label for="journalId">Journal ID</label>
            <input type="text"  id="journalId" name="journalId" class="form-control input-lg" readonly>
          </div>

          <div class="form-group">
            <label for="requsition">Requsition Note</label>
            <select name="requsition" id="requsition" class="form-control input-lg" required></select>
          </div>

          <div class="form-group">
            <label for="barcode">Barcode</label>
            <input type="text" name="barcode" id="barcode" class="form-control input-lg" required autocomplete="off">
          </div>
          <input type="hidden" name="requsition_value" id="requsition_value">
          <button class="btn btn-primary btn-lg btn-block" id="completeJournal" type="button">Complete</button>
        </form>
      </div>
    </div>

  </div>
  <div class="col-md-6 col-sm-6">
    <h3 class="head-text">รายการล่าสุด</h3>
    <div id="grid_latest" class="hide"></div>
  </div>
</div>


<script>

jQuery(document).ready(function($) {

  if (!!qs('j')) {
    $('#journalId').val(qs('j'));
    $('#requsition').val();
    $('#panel-new').addClass('hide');
    $('#panel-barcode').removeClass('hide');

    $('#grid_latest').removeClass('hide');
    grid_latest(qs('j'));

    // setInterval(function() {
    //   gojax('get', base_url+'/api/movement/issue/'+$())
    // }, 1000);

    gojax('get', base_url + "/api/requsition_note/all")
      .done(function(data) {
        $('#requsition').html('<option value="">= กรุณาเลือก =</option>');
        $.each(data, function(index, el) {
         $('#requsition').append('<option value="'+el.ID+'">'+el.Description+'</option>');
        });
      });
  } else {
    gojax('get', base_url + "/api/employee/all/by_status")
      .done(function(data) {
        $('#employee_code').html('<option value="">= กรุณาเลือก =</option>');
        $.each(data, function(index, el) {
         $('#employee_code').append('<option value="'+el.Code+'">'+el.Name+'</option>');
        });
      });
  }

  $('#completeJournal').on('click', function(event) {

    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Yes": function() {
          gojax('post', base_url+'/api/movement_issue/complete', {
            journalId: $('#journalId').val()
          })
          .done(function(data) {
            if (data.status == 200) {
              window.location = base_url+'/movement/issue';
            } else {
              $('#modal_alert').modal({backdrop: 'static'});
              $('#modal_alert_message').text(data.message);
              $('#top_alert').hide();
            }
          })
          .fail(function() {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text('ไม่สามารถส่งข้อมูลได้');
            $('#top_alert').hide();
          });

          $('select#requsition').val('');
          $('#requsition_value').val('');
          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });

  $('#modal_alert').on('click', function(event) {
    event.preventDefault();
    $('select#requsition').val('');
    $('#requsition_value').val('');
    $('#barcode').val('').focus();
  });

	$('#barcode').keydown(function(event) {

		if (event.which === 13) {
      if (!!$('#requsition_value').val()) {

        gojax_f('post', base_url+'/api/movement/issue/save', '#formBarcode')
        .done(function(data) {
          if (data.status != 200) {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
            $('#top_alert').hide();
          } else {
            $('#top_alert').show();
            $('#top_alert_message').text('Barcode ล่าสุด '+ $('#barcode').val());
            $('#modal_alert').modal('hide');
            $('#grid_latest').jqxGrid('updatebounddata');
            $('#barcode').val('').focus();
          }

          $('select#requsition').val('');
          $('#requsition_value').val('');
          $('#barcode').val('').focus();
        });

      } else {
        $('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณาเลือก Requsition Note ด้วยค่ะ!');
        $('#top_alert').hide();
      }
     // End
    }

	});

	$('#selectEmp').on('click', function(event) {
		event.preventDefault();
		$('#modal_employee').modal({backdrop: 'static'});
		grid_employee();
	});

	$('#modal_note').on('shown.bs.modal', function(event) {
		event.preventDefault();
		grid_requsition();
	});

	$('#requsition').on('change', function(event) {
     $('#requsition_value').val($('#requsition').val());
     $('#barcode').focus();
	});

	$('#employee_code').on('change', function(event) {

    gojax('get', base_url+'/api/employee/'+$('#employee_code').val()+'/division')
      .done(function(data) {
        $.each(data, function(index, val) {
          open_button();
          $('#division').val(val.Description);
          $('#division_value').val(val.Code);
        });
      })
      .fail(function() {
        $('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('ไม่สามารถดึงข้อมูลได้ กรุณาลองอีกครั้ง');
        $('#top_alert').hide();
        close_button();
      });

    $('#user').val('').focus();
	});

  $('#user').keydown(function(event) {

    if (event.which === 13) {
      $('#pass').val('').focus();
    }

  });
  // $('input[name=pass]').keydown(function($) {
  // });




    	$('#formNewIssue').on('submit', function(event) {




    event.preventDefault();
    // $('#employee_code').css({'background': '#eeeeee'}).prop('readonly', true);
    // $('#user').css({'background': '#eeeeee'}).prop('readonly', true);
    // $('#pass').css({'background': '#eeeeee'}).prop('readonly', true);
    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 600,
      modal: true,
      buttons: {
        "Yes": function() {

          gojax_f('post', base_url+'/api/journal/table/save', '#formNewIssue')
            .done(function(data) {
              if (data.status === 200) {

                // $('#modal_barcode').modal({backdrop: 'static'});
                $('#journalId').val(data.journal);
                $('#requsition').val();
                // $('#barcode').focus();
                // window.location = '?s=1&jid='+data.journal;
                $('#panel-new').addClass('hide');
                $('#panel-barcode').removeClass('hide');

                $('#grid_latest').removeClass('hide');

                grid_latest(data.journal);

                gojax('get', base_url + "/api/requsition_note/all")
                  .done(function(data) {

                    $('#requsition').html('<option value="">= กรุณาเลือก =</option>');
                    $.each(data, function(index, el) {
                     $('#requsition').append('<option value="'+el.ID+'">'+el.Description+'</option>');
                    });
                  });
              } else {
                $('#modal_alert').modal({backdrop: 'static'});
                $('#modal_alert_message').text(data.message);
                $('#top_alert').hide();
                $('form#formNewIssue').trigger('reset');
              }
            });

          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }

    });

	});

});


function grid_latest(journalId) {

  var dataAdapter = new $.jqx.dataAdapter({
  datatype: 'json',
      datafields: [
        { name: 'BarcodeID', type: 'string'},
        { name: 'CuringCode', type: 'string' }
      ],
      url: base_url + '/api/movement_issue/'+journalId+'/latest'
  });

  return $("#grid_latest").jqxGrid({
    width: '100%',
    source: dataAdapter,
    autoheight: true,
    pageSize : 10,
    // rowsheight : 40,
    // columnsheight : 40,
    altrows : true,
    pageable : true,
    sortable: true,
    filterable : true,
    showfilterrow : true,
    columnsresize: true,
    // theme : 'theme',
    columns: [
      { text: 'Barcode', datafield: 'BarcodeID', width: 150},
      { text: 'Curing Code', datafield: 'CuringCode', width: 150}
    ]
  });
}

function grid_employee() {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'Code', type: 'string'},
          { name: 'FirstName', type: 'string' },
          { name: 'LastName', type: 'string'},
          { name: 'Name', type: 'string'},
          { name: 'DivisionCode', type: 'string' },
          { name: 'DepartmentCode', type: 'string'},
          { name: 'DivisionDesc', type: 'string' }
        ],
        url: base_url + "/api/employee/all/by_status"
  });

  return $("#grid_employee").jqxGrid({
        width: '100%',
        source: dataAdapter,
        autoheight: true,
        pageSize : 10,
        // rowsheight : 40,
        // columnsheight : 40,
        altrows : true,
        pageable : true,
        sortable: true,
        filterable : true,
        showfilterrow : true,
        columnsresize: true,
        // theme : 'theme',
        columns: [
          { text: 'ID', datafield: 'Code', width: 100},
          { text: 'Name', datafield: 'Name', width: 200},
          { text: 'Division', datafield: 'DivisionDesc', width: 100 },
        ]
  });
}

function grid_requsition() {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'ID', type: 'number'},
          { name: 'Description', type: 'string' }
        ],
        url: base_url + "/api/requsition_note/all"
  });

  return $("#grid_requsition").jqxGrid({
        width: '100%',
        source: dataAdapter,
        autoheight: true,
        pageSize : 10,
        // rowsheight : 40,
        // columnsheight : 40,
        altrows : true,
        pageable : true,
        sortable: true,
        filterable : true,
        showfilterrow : true,
        columnsresize: true,
        // theme : 'theme',
        columns: [
          { text: 'ID', datafield: 'ID', width: 100},
          { text: 'Description', datafield: 'Description', width: 200}
        ]
  });
}

</script>
