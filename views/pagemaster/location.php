<?php $this->layout("layouts/base", ['title' => 'Location']); ?>

<div class="row">
  <div class="col-md-12">
    <h1>Location</h1>
    <hr />
    <div class="btn-panel">
      <button onclick="return modal_create_open()"  class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_loca_create">Create</button>
      <button class="btn btn-info" id="edit">Edit</button>
    </div>
    <div id="gridlocation"></div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_loca_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
      	<form id="form_create_loca" onsubmit="return submit_create_loca()">
      		<div class="form-group">
      			<label for="loca_name">Description</label>
      			<input type="text" name="loca_name" id="loca_name" class="form-control" autocomplete="off" required>
      		</div>
      		<div class="form-group">
      			<label for="wh_name">Warehouse</label>
      			<select name="wh_name" id="wh_name" class="form-control" required></select>
      		</div>
          <div class="form-group">
            <label for="receive_name">ReceiveLocation</label>
            <input type="number" name="receive_name" id="receive_name" class="form-control" />
          </div>
          <div class="form-group">
            <label for="disposal">Disposal</label>
            <select name="disposal" id="disposal" class="form-control"></select>
          </div>
      		<div class="form-group">
      			<label>
      			<input type="checkbox" name="auto_issue" id="auto_issue" checked> Auto Issue 
      			</label>
      		</div>
      		<div class="form-group">
      			<label>
      			<input type="checkbox" name="loca_use" id="loca_use" checked> In Use 
      			</label>
      		</div>
      		<input type="hidden" name="form_type">
      		<input type="hidden" name="loca_id">
      		<button class="btn btn-primary">Save</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($){
		gridlocation();
    // grid_wh();

		getwarehouse()
		.done(function(data) {
			$('select[name=wh_name]').html("<option value=''>-- Select --</option>");
			$.each(data, function(index, val) {
				$('select[name=wh_name]').append('<option value="'+val.ID+'">'+val.Description+'</option>');
			});
		});

		$('#edit').on('click', function(e) {	
        $('.modal-title').text('Update');
        var rowdata = row_selected("#gridlocation");
        if (typeof rowdata !== 'undefined') {
          $('#modal_loca_create').modal({backdrop: 'static'});
          $('input[name=form_type]').val('update');
          $('input[name=loca_id]').val(rowdata.ID);
          $('input[name=loca_name]').val(rowdata.Description);
          $('select[name=wh_name]').val(rowdata.WarehouseID);
          $('input[name=receive_name]').val(rowdata.ReceiveLocation);

          if (rowdata.InUse==1){
            $('input[name=loca_use]').prop('checked' , true);
          }else if(rowdata.InUse==0){
               $('input[name=loca_use]').prop('checked' , false);
          }

          if (rowdata.AutoIssue==1){
              $('input[name=auto_issue]').prop('checked' , true);
          }else if(rowdata.AutoIssue==0){
               $('input[name=auto_issue]').prop('checked' , false);
          }

          gojax('get', base_url+'/api/disposal/all')
            .done(function(data) {
              $('#disposal').html('');
              for (var i = 0; i < data.length; i++ ) {
                $('#disposal').append('<option value="'+data[i].ID+'">'+data[i].DisposalDesc+'</option>');
              }
              $('#disposal').val(rowdata.DisposalID);
            });
        }
        
		  });

	});

	function getwarehouse() {
		return $.ajax({
			url : base_url + '/api/warehouse/all',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}

	function modal_create_open() {
		$('#form_create_loca').trigger('reset');
		$('input[name=form_type]').val('create');

    gojax('get', base_url+'/api/disposal/all')
      .done(function(data) {
        $('#disposal').html('<option value="">-- Select --</option>');
        for (var i = 0; i < data.length; i++ ) {
          $('#disposal').append('<option value="'+data[i].ID+'">'+data[i].DisposalDesc+'</option>');
        }
      });
	}

	function submit_create_loca() {
		var	loca_name = $('input[name=loca_name]').val();
    var wh_name = $('select[name=wh_name]').val();
        //alert(loca_name+loca_use);
		if (!!loca_name) {
			$.ajax({
				url : base_url + '/api/location/create',
				type : 'post',
				cache : false,
				dataType : 'json',
				data : $('form#form_create_loca').serialize()
			})
			.done(function(data) {
				if (data.status != 200) {
					// gotify(data.message, 'danger');
					alert(data.message);
				} else {
					$('#modal_loca_create').modal('hide');
					$('#gridlocation').jqxGrid('updatebounddata');
				}
			});
		}
		return false;
	}

	function gridlocation(){
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
        datafields: [
            { name: "ID", type: "number"},
            { name: "Description", type: "string" },
            { name: "WarehouseID", type: "int" },
            { name: "ReceiveLocation", type:"int"},
            { name: "AutoIssue", type:"bool"},
            { name: "InUse", type:"bool"},
            { name: 'DisposalID', type: 'number'},
            { name: 'DisposalDesc', type: 'string'},
            { name: "DescriptionWH", type:"string"},
            { name: 'ReverseReceiveLocation', type: 'number'},
            { name: 'ReturnReceiveLocation', type: 'number'},
            { name: 'UnpickReceiveLocation', type: 'number'}
        ],
        updaterow: function(rowid, rowdata, commit) {
          gojax('post', base_url+'/api/location/'+rowdata.ID+'/edit', {
            reverse: rowdata.ReverseReceiveLocation,
            return: rowdata.ReturnReceiveLocation,
            unpick: rowdata.UnpickReceiveLocation
          })
          .done(function(data) {
            if (data.status !== 200) {
              $('#top_alert').hide();
              $('#modal_alert').modal({backdrop: 'static'});
              $('#modal_alert_message').text(data.message);
            } else {
              $('#gridlocation').jqxGrid('updatebounddata');
            }
          });
          commit(true); 
        },
        url : base_url+'/api/location/all'
	});

	return $("#gridlocation").jqxGrid({
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
        // theme: 'theme',
        columns: [
          { text:"ID", datafield: "ID" , width: 50, editable: false},
          { text:"Warehouse", datafield: "DescriptionWH" , width: 130, editable: false},
          { text:"Location", datafield: "Description" , width: 130, editable: false},
          
          { text:"Receive Location", datafield: "ReceiveLocation", width: 130, editable: false},
          { text:"Auto Issue", datafield: "AutoIssue" , filtertype: 'bool', columntype: 'checkbox', width: 100, editable: false},
          { text:"In Use", datafield: "InUse" , filtertype: 'bool', columntype: 'checkbox', width: 100, editable: false},
          { text: 'Disposal', datafield: 'DisposalDesc', width: 100, editable: false},
          { text: 'Reverse Receive Location', datafield: 'ReverseReceiveLocation', width: 200},
          { text: 'Return Receive Location', datafield: 'ReturnReceiveLocation', width: 200},
          { text: 'Unpick Receive Location', datafield: 'UnpickReceiveLocation', width: 200}
        ]
	    });

	}

  function grid_wh() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'ID', type: 'number'},
            { name: 'Description', type: 'string' },
            { name: 'Company', type: 'string'}
          ],
          url: base_url + "/api/warehouse/all"
    });

    return $("#grid_wh").jqxGrid({
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
            { text: 'Description', datafield: 'Description', width: 200},
            { text: 'Company', datafield: 'Company', width: 100}
          ]
      });
  }
</script>