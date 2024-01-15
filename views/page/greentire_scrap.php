<?php $this->layout("layouts/base", ['title' => 'Scrap']); ?>

<form class="form-center" onsubmit="return form_scarp_submit()">
	<h1>Greentire Scarp</h1>
	<div class="form-group">
		<div id="grid_defect"></div>
	</div>

    <div class="modal" id="dialog_barcode" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	            <span aria-hidden="true">&times;</span>
	        </button>
	        <h4 class="modal-title">Greentire Scrap</h4>
	      </div>
	      <div class="modal-body">
	        <div class="input-group">
		      <input type="text" class="form-control" placeholder="BarCode..." name="search" id='barcode'>
		      <span class="input-group-btn">
		        <button class="btn btn-primary" id="save" type="button" data-toggle="modal" data-target="#myModal">Click >></button>
		      </span>
		    </div>
	      </div>
	    </div>
	  </div>
	</div>
</form>

<script type="text/javascript">
var defectcode;
jQuery(document).ready(function($) {
	
	grid_defect();
	$('#grid_defect').on('rowdoubleclick', function(e) {
		var rowdata = row_selected('#grid_defect');
		defectcode = rowdata.ID;
	    if (!!rowdata && defectcode) {
	    	$('#dialog_barcode').modal('show');
	    };
	});
}); 
    function grid_defect() {
    	
		var dataAdapter = new $.jqx.dataAdapter({
			dataType : 'json',
	        datafields: [
	        	{ name: 'ID', type: 'string'},
	        	{ name: 'Description', type: 'string' }
	        ],
	        url: base_url + "/api/defect/all"
		});
		return $("#grid_defect").jqxGrid({
	        width:500,
	        source: dataAdapter, 
	        autoheight: true,
	        pageable: true,
	        pagesize: 5,
	        theme : 'theme',
	        columns: [
	          { text: 'ID', datafield: 'ID', width: 100},
	          { text: 'Description', datafield: 'Description', width: 400}
	        ]
	    });
	}
	$('#save').on('click',function(e){
	e.preventDefault();
          $.ajax({
                url : base_url + '/api/greentirescrap',
                type : 'post',
                dataType : 'json',
                cache : false,
                data : {
                    barcode  : $('#barcode').val(),
                    tostatus : 4,
                    disposalid : 2,
                    defectcode : defectcode
                },
                success : function(data){
                   if (data.status == 404) {
                   	   alert(data.message);
                       $('#barcode').val('');
               	   }
               	   else
               	   {
               	   	   alert('No Complete');
               	   }
                },
                error : function(data){
                   alert(data);
                }
          });
	});
	
</script>