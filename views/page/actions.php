<?php $this->layout("layouts/base", ['title' => 'Actions']); ?>

<div class="head-space"></div>
<div class="panel panel-default">
	<div class="panel-heading">Actions</div>
	<div class="panel-body">
      <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
    <li role="presentation" class="active"><a href="#actions_main" aria-controls="actions_main" role="tab" data-toggle="tab">All Actions</a></li>
    <li role="presentation"><a href="#menu_lists" aria-controls="menu_lists" role="tab" data-toggle="tab">Menu Lists</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="actions_main">
         <div class="btn-panel">
            <button class="btn btn-success" id="create">Create new</button>
        </div>
        <div id="grid_actions"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="menu_lists">
        <div id="grid_menu"></div>
    </div>
  </div>
       
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		
        grid_actions();
        grid_menu();

        $('#create').on('click', function() {
            if (confirm('Are you sure?')) {
                gojax('post', base_url+'/api/actions/create')
                    .done(function(data) {
                        if (data.status === 200) {
                            $('#grid_actions').jqxGrid('updatebounddata', 'cells');
                        }
                    });
            }
        });
	});

	function grid_actions() {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
            datafields: [
            	{ name: 'id', type: 'number'},
            	{ name: 'description', type: 'string' },
                { name: 'slug', type: 'string'},
                { name: 'menu_id', type: 'number'},
                { name: 'status', type: 'number'}
            ],
            url: base_url + "/api/actions",
            updaterow: function (rowid, rowdata, commit) {
                gojax('post', base_url+'/api/actions/edit', {
                    id: rowdata.id,
                    description: rowdata.description,
                    slug: rowdata.slug,
                    menu_id: rowdata.menu_id,
                    status: rowdata.status
                })
                .done(function(data) {
                    $('#grid_actions').jqxGrid('updatebounddata', 'cells');
                    commit(true);
                })
                .fail(function() {
                    commit(false);
                });
            },
		});

		return $("#grid_actions").jqxGrid({
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
              { text: 'id', datafield: 'id', width: 100, editable : false},
              { text: 'description', datafield: 'description', width: 300},
              { text: 'slug', datafield: 'slug', width: 300},
              { text: 'menu id', datafield: 'menu_id', width: 100},
              { text: 'status', datafield: 'status', width: 100, editable : false}
            ]
        });
	}

    function grid_menu() {
        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [
                { name: 'ID', type: 'number'},
                { name: 'Link', type: 'string'},
                { name: 'Description', type: 'string' },
                { name: 'Status', type: 'string'}
            ],
            url: base_url + "/api/menu/all"
        });

        return $("#grid_menu").jqxGrid({
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
            // theme: 'theme',
            columns: [
              { text: 'ID', datafield: 'ID', width: 100},
              { text: 'Description', datafield: 'Description', width: 200},
              { text: 'Link', datafield: 'Link', width: 200}
            ]
        });
    }
</script>