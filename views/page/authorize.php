<?php $this->layout("layouts/base", ['title' => 'Authorize']); ?>

<h1>Authorize Master</h1>

<div class="btn-panel">
	<button onclick="return onCreateOpen()" class="btn btn-success btn-lg" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
	<!-- <button class="btn btn-info" id="edit">Edit</button> -->
</div>

<div id="grid_authorize"></div>

<!-- Modal Create -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="formAuthorize">
          <div class="form-group">
          	<label for="description">Description</label>
          	<input id="description" name="description" type="text" class="form-control" placeholder="Description" autocomplete="off"  required>
          </div>

          <div class="form-group">
            <label for="type">Type</label><br>
            <select name="type[]" id="type" class="_select" multiple="multiple" style="width: 300px;">
            </select>
          </div>
          
          <input type="hidden" name="_id" id="_id" value="">
          <button class="btn btn-primary" type="submit">Save</button>
        </form>

      </div>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function($) {

    grid_authorize();

    $('form#formAuthorize').on('submit', function(event) {
      event.preventDefault();
      if (!!$('#description').val()) {
        gojax_f('post', base_url+'/api/authorize/create', '#formAuthorize')
          .done(function(data) {
            if (data.status === 200) {
              alert(data.message);
              $('#modal_create').modal('hide');
              $('#grid_authorize').jqxGrid('updatebounddata');
            } else {
              alert(data.message);
            }
          })
          .fail(function() {
            $('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text('ไม่สามารถส่งข้อมูลได้');
          });
      }
    });

  });

  function grid_authorize() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'ID', type: 'string'},
            { name: 'Description', type: 'string' },
            { name: 'Unhold_Unrepair_GT', type: 'bool'},
            { name: 'Unhold_Unrepair_Final', type: 'bool'},
            { name: 'Adjust_GT', type: 'bool'},
            { name: 'Adjust_Final', type: 'bool'},
            { name: 'Adjust_FG', type: 'bool'},
            { name: 'Loading', type: 'bool'},
            { name: 'MovementReverse', type: 'bool'},
            { name: 'Unbom', type: 'bool'},
            { name: 'PostbackMovement', type: 'bool'}
          ],
          updaterow: function(rowid, rowdata, commit) {
            // console.log(rowdata);
            gojax('post', base_url+'/api/authorize/'+rowdata.ID+'/edit', {
              Description: rowdata.Description,
              Unhold_Unrepair_GT: rowdata.Unhold_Unrepair_GT,
              Unhold_Unrepair_Final: rowdata.Unhold_Unrepair_Final,
              Adjust_GT: rowdata.Adjust_GT,
              Adjust_Final: rowdata.Adjust_Final,
              Adjust_FG: rowdata.Adjust_FG,
              Loading: rowdata.Loading,
              MovementReverse: rowdata.MovementReverse,
              Unbom: rowdata.Unbom,
              PostbackMovement: rowdata.PostbackMovement
            })
            .done(function(data) {
              // console.log(data);
              if (data.status !== 200) {
                $('#top_alert').hide();
                $('#modal_alert').modal({backdrop: 'static'});
                $('#modal_alert_message').text(data.message);
              } else {
                $('#grid_authorize').jqxGrid('updatebounddata');
              }
            });
            commit(true);
          },
          url: base_url + "/api/authorize/all"
    });

    return $("#grid_authorize").jqxGrid({
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
          editable: true,
          // theme : 'theme',
          columns: [
            { text: 'Description', datafield: 'Description', width: 300},
            { text: 'Unhold Unrepair GT', datafield: 'Unhold_Unrepair_GT', width: 150, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Unhold Unrepair Final', datafield: 'Unhold_Unrepair_Final', width: 150, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Loading', datafield: 'Loading', width: 100, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Adjust GT', datafield: 'Adjust_GT', width: 100, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Adjust Final', datafield: 'Adjust_Final', width: 100, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Adjust FG', datafield: 'Adjust_FG', width: 100, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Movement Reverse', datafield: 'MovementReverse', width: 200, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Unbom', datafield: 'Unbom', width: 100, columntype: 'checkbox', filtertype: 'bool'},
            { text: 'Postback Movement', datafield: 'PostbackMovement', width: 100, columntype: 'checkbox', filtertype: 'bool'}
            
          ]
      });
  }

  function onCreateOpen() {
    $('#_id').val('');
    $('#type').html('');
    $('#formAuthorize').trigger('reset');

    gojax('get', base_url+'/api/authorize/field').done(function(data) {
      $.each(data, function(index, val) {
        $('#type').append('<option value="'+index+'">'+val.COLUMN_NAME+'</option>');
      });
      $('select._select').multipleSelect({placeholder: 'เลือกข้อมูล'});
    });    
  }
</script>