<?php $this->layout("layouts/base", ['title' => 'Component']);
?>
<style>
	td {
		padding: 5px;
	}
</style>
<h1 class="head-text">Component Partcode</h1>
<hr>
<!-- Modal -->
<div class="modal" id="modal_form_item" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Create</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="form_create_item"  onsubmit="return submit_create_item()">
        	<table>
        		<tr>
        			<td>
        				<label>PartCode</label>
        			</td>
        			<td>
        				<input type="text" name="pastcode" id="pastcode" class="form-control" required autocomplete="off">
        			</td>
        		</tr>
        		<tr>
        			<td>
        				<label>Item</label>
        			</td>
        			<td>
        				<input type="text" name="item" id="item" class="form-control" required autocomplete="off">
        			</td>
        		</tr>
        		<tr>
        			<td>
        				<label>ItemName</label>
        			</td>
        			<td>
        				<input type="text" name="item_name" id="item_name" class="form-control" required autocomplete="off">
        			</td>
        		</tr>
        		<tr>
        			<td>
        				<label>Section</label>
        			</td>
        			<td>
        				<select name="section_id" id="section_id" class="form-control" required></select>
        			</td>
        		</tr>
        		<tr>
        			<td>
        				<br>
        				<input type="hidden" name="form_type" id="form_type">
        				<input type="hidden" name="id" id="id">
        				<button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
        			</td>
        		</tr>
        	</table>

        	
        </form>
      </div>
    </div>
  </div>
</div>

<form id="form_filter">
	<div class="panel-body">
		<select name="section[]" id="section"  multiple="multiple" style="width: 270px;"></select>
		<button class="btn btn-warning" id="search"> 
			<span class="glyphicon glyphicon-search"></i> ค้นหา
		</button>
		<br><br>
		<button class="btn btn-info" id="print">
			<span class="glyphicon glyphicon-print"></span> Print
		</button>
		<button class="btn btn-success" id="create">
			<span class="glyphicon glyphicon-plus"></span> Create
		</button>
		<button class="btn btn-danger" id="update">
			<span class="glyphicon glyphicon-edit"></span> Update
		</button>
	</div>	
</form>

<div id="griditem"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var section = '';

		griditem(section);

		$('#section').html("");
	  	getSectionComponent()
		.done(function(data) {
			$.each(data, function(k, v) {
				$('#section').append('<option value="'+ v.SectionID +'">'+v.SectionName+'</option>');
			});
			$('#section').multipleSelect({
				placeholder: 'เลือกข้อมูล', 
				filter: true,
				position: 'buttom'
			});
		});

		$('#search').on('click', function() {
			griditem($('#section').val());
			return false;
		});

		$("#print").on('click', function() {
			var rowdata = row_selected("#griditem");
			var rows_selected = [];
			var row_item = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#griditem').jqxGrid('getselectedrowindexes');
				
				for (var i = 0; i < rows.length; i++) {
					row_item = $('#griditem').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_item.ID);
				}

				window.open("/component/print/pastcode?item="+rows_selected, "_blank");
				// console.log(rows_selected);
			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

		$("#create").on('click', function() {
			$("#modal_form_item").modal({backdrop: "static"});
			$('#form_type').val('create');
			$('#form_create_item').trigger('reset');
			gojax('get', '/component/section')
			.done(function(data) {
				$('select[name=section_id]').html("<option value=''>=Select=</option>");
				$.each(data, function(index, val) {
					$('select[name=section_id]').append('<option value="'+val.SectionID+'">'+val.SectionName+'</option>');
				});	
				// $('select[name=section_id]').val(rowdata.SectionID);
			});
			return false;
		});

		$("#update").on('click', function() {
			var rowdata = row_selected('#griditem');
			if (!!rowdata) {
				$("#modal_form_item").modal({backdrop: "static"});
				$('#form_type').val('update');
				$('#pastcode').val(rowdata.PastCodeID);
				$('#item').val(rowdata.ItemID);
				$('#item_name').val(rowdata.ItemName);
				$('#id').val(rowdata.ID);
				gojax('get', '/component/section')
				.done(function(data) {
					$('select[name=section_id]').html("<option value=''>=Select=</option>");
					$.each(data, function(index, val) {
						$('select[name=section_id]').append('<option value="'+val.SectionID+'">'+val.SectionName+'</option>');
					});	
					$('select[name=section_id]').val(rowdata.SectionID);
				});

			}
			return false;
		});

	});

	function submit_create_item(){
	 
	  $.ajax({
	    url : '/component/create/item',
	    type : 'post',
	    cache : false,
	    dataType : 'json',
	    data : $('form#form_create_item').serialize()
	  })
	  .done(function(data) {
	    if (data.status==200) {
	    	$('#griditem').jqxGrid('updatebounddata');
	    	$('#modal_form_item').modal('hide');
	    }else{
	    	alert(data.message);
	    }
	    // console.log(data);
	  });
	  // alert($('form#form_create_item').serialize());
	  return false;
	}

	function getSectionComponent() {
		return $.ajax({
			url : '/component/section',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}

	function griditem(section) {

		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
		datafields: [
	      { name: "ID", type: "int" },
	      { name: "PastCodeID", type: "string" },
  		  { name: "ItemID", type: "string" },
          { name: "ItemName", type: "string" },
          { name: "Warehouse", type: "string" },
          { name: "Location", type: "string" },
          { name: "SectionID", type: "int" },
          { name: "SectionName", type: "string" }
		],
		url : '/component/load/pastcode?section='+section
		});

		return $("#griditem").jqxGrid({
			width: '60%',
		    source: dataAdapter, 
		    autoheight: true,
		    pageSize : 10,
		    altrows : true,
		    pageable : true,
		    sortable: true,
		    filterable : true,
		    showfilterrow : true,
		    columnsresize: true,
		    selectionmode: 'checkbox',
			columns: [
        		{ text:"PartCode", datafield: "PastCodeID", width:'20%'},
			  	{ text:"Item", datafield: "ItemID", width:'20%'},
        		{ text:"ItemName", datafield: "ItemName"}
			]
		});
	}
</script>