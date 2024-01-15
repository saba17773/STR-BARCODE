<?php $this->layout("layouts/base", ['title' => 'FIFO Batch']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">FIFO Batch</div>
  <div class="panel-body">
    <div class="btn-panel">
        <button class="btn btn-success" id="Add">New</button>
        <button class="btn btn-primary" id="Update">Update</button>
    </div>
     <div id="grid_fifo"></div>
  </div>
</div>

<div class="modal" id="modal_add" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new FIFO Batch</h4>
      </div>
      <div class="modal-body">
        <form id="formNewIssue">
          <div class="form-group">
            <label for="product_group">Product Group</label>
            <select name="product_group" id="product_group" class="form-control"  required>
				<option value="0">-- กรุณาเลือกกลุ่มของสินค้า --</option>
				<option value="TBR">TBR</option>
				<option value="RDT">RDT</option>
			</select>
          </div>
          <div class="form-group">
            <label for="qty_min">QTY Min</label>
            <input type="text" class="form-control" name="qty_min" id="qty_min" autocomplete="off"  required>
          </div>
          <div class="form-group">
            <label for="aging_date">Aging Date</label>
            <input type="text" class="form-control" name="aging_date" id="aging_date" autocomplete="off"  required>
          </div>
          <div class="form-group">
		  	<!-- <label for="chkClick" type="hidden">ID</label> -->
            <input type="hidden" class="form-control" name="chkClick" id="chkClick" value="0"  readonly>
          </div>
            <label>
              <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
            </label>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		grid_fifo();
		setInt('#qty_min');
		setInt('#aging_date');

		$('#Add').on('click', function() {
			$('#modal_add').modal({backdrop: 'static'});
			$("#formNewIssue")[0].reset();
			$('#product_group').prop('disabled', false);
		});

		$('#Update').on('click', function() {
			var rowdata = row_selected('#grid_fifo');
			if (typeof rowdata !== 'undefined') {
				$('#modal_add').modal({backdrop: 'static'});
				$('#qty_min').val(rowdata.QTYMin);
				$('#aging_date').val(rowdata.Aging);
				$('#product_group').val(rowdata.ProductGroup);
				$('#chkClick').val(rowdata.ID);
				
				$('#product_group').prop('disabled', true);
			} else {
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
		});

		function grid_fifo() {
			var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
				datafields: [
					{ name: 'QTYMin', type: 'string'},
					{ name: 'Aging', type: 'string'},
					{ name: 'ProductGroup', type: 'string' },
					{ name: 'ID', type: 'string' }
				],
				url: base_url + "/api/FIFOBatch/all"
			});

			return $("#grid_fifo").jqxGrid({
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
					{ text: 'ProductGroup', datafield: 'ProductGroup', width: 100},
					{ text: 'QTY Min', datafield: 'QTYMin', width: 200},
					{ text: 'Aging Date', datafield: 'Aging', width: 100}
				]
				});
		}

		$('#formNewIssue').on('submit', function(event) {
				
			$('#modal_add').modal('hide');
			event.preventDefault();

			var qty = $('input[name=qty_min]').val();
			var aging_date = $('input[name=aging_date]').val();
			var product_group = $('select[name=product_group]').val();
			var id = $('input[name=chkClick]').val();

			$( "#dialog-confirm" ).dialog({
				resizable: false,
				height: "auto",
				width: 600,
				modal: true,
				buttons: {
					"Yes": function() {
						if (id === "0"){ 
							gojax_f('post', base_url+'/fifobatch/chkProductGrp/'+ product_group, '#formNewIssue')
							.done(function(chk){ 
								if(chk.status == 200){ 
									gojax_f('post', base_url+'/fifobatch/insertData/'+ product_group + '/' + qty + '/' + aging_date, '#formNewIssue')
									.done(function(data) { 
										if(data.status == 200){
											$('#grid_fifo').jqxGrid('updatebounddata');
										} else {
											alert("data not insert!");
										}
									});
								} else {
									alert("มี Product Group นี้ในระบบแล้ว");
								}
							});
						}else {
							gojax_f('post', base_url+'/fifobatch/updateData/'+ product_group + '/' + qty + '/' + aging_date + '/' + id, '#formNewIssue')
							.done(function(data) {
								if(data.status == 200){
									$('#grid_fifo').jqxGrid('updatebounddata');
								} else {
									alert("data not update!");
								}
							});
						}
						$( this ).dialog( "close" );
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		});
});
</script>
