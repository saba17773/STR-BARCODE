<?php $this->layout("layouts/base", ["title" => "Gate"]); ?>

<h1>Gate</h1>

<div class="btn-panel">
	<button class="btn btn-success btn-lg"
		data-backdrop="static" data-toggle="modal" data-target="#modal_creat">Create</button>
	<button class="btn btn-info btn-lg" style="display: none;">Edit</button>
</div>

<div id="grid_gate"></div>

<!-- Modal -->
<div class="modal" id="modal_creat" tabindex="-1" role="dialog">
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
        <form id="formGate">
        	<div class="form-group">
        		<label for="description">Description</label>
						<input type="text" name="description" id="description" class="form-control" required>	
        	</div>
        	<button class="btn btn-primary btn-lg" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {

		grid_gate();
		
		$('#modal_creat').on('shown.bs.modal', function(event) {
			event.preventDefault();
			$('#description').val('').focus();
		});

		$('#formGate').on('submit', function(event) {
			event.preventDefault();
			var description = $('#description').val();
			if (!!description) {
				gojax('post', base_url+'/api/gate/save', {
					description: description
				})
				.done(function(data) {
					if (data.status === 200) {
						$('#grid_gate').jqxGrid('updatebounddata');
						$('#modal_creat').modal('hide');
					}
				});
			}
		});

	});

	function grid_gate() {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
        datafields: [
        	{ name: 'ID', type: 'string'},
        	{ name: 'Description', type: 'string' }
        ],
        url: base_url + "/api/gate/all"
		});

		return $("#grid_gate").jqxGrid({
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
        { text: 'Description', datafield: 'Description', width: 100}
      ]
    });
    
	}
</script>