<?php $this->layout("layouts/base", ['title' => 'Permission']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
	<div class="panel-heading">Permission</div>
	<div class="panel-body">
		<div class="btn-panel">
			<button onclick="return modal_create_open()"  class="btn btn-success" id="create">Create</button>
			<button class="btn btn-info" id="edit">Edit</button>
		</div>

		<div id="grid_per"></div>
	</div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_per_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
      	<form id="form_create_per" onsubmit="return submit_create_per()">

      		<div class="form-group">
      			<label for="des_name">Description</label>
      			<input type="text" name="des_name" id="des_name" class="form-control" autocomplete="off" required>
      		</div>

      		<table class="table">
      			<tr>
      				<td>
      					Permission for Desktop
      				</td>
      				<td>
      					<select name="permission_desktop[]" multiple="multiple" class="select_multiple" id="permission_desktop" style="width:300px;"></select>
      				</td>
      			</tr>
      			<tr>
      				<td>
      					Permission for Mobile
      				</td>
      				<td>
      					<select name="permission_mobile[]" multiple="multiple" class="select_multiple" id="permission_mobile" style="width:300px;"></select>
      				</td>
      			</tr>
      			<tr>
      				<td>
								Default Page Desktop
							</td>
							<td>
								<select name="default_page_desktop" id="default_page_desktop" class="select_single" style="width:300px;"></select>
							</td>
      			</tr>
      			<tr>
      				<td>
								Default Page Mobile
							</td>
							<td>
								<select name="default_page_mobile" id="default_page_mobile" class="select_single" style="width:300px;"></select>
							</td>
      			</tr>
      			<tr>
      				<td>
								User actions
							</td>
							<td>
								<select name="user_actions[]" multiple="multiple" id="user_actions" style="width:300px;"></select>
							</td>
      			</tr>
      		</table>

        	<div class="form-group">
      			<label>
      			<input type="checkbox" name="status" id="status" checked> Status 
      			</label>
      		</div>

      		<input type="hidden" name="form_type">
      		<input type="hidden" name="per_id">
      		<button class="btn btn-primary">Save</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($){

		var permission_desktop_selected = [];
		var permission_mobile_selected = [];
		
		grid_per();
		
		$('#edit').on('click', function(e) {	

			var rowdata = row_selected("#grid_per");

			if (typeof rowdata !== 'undefined') {

				$('#modal_per_create').modal({backdrop: 'static'});
				$('input[name=form_type]').val('update');
				$('.modal-title').text('Update');
				$('input[name=per_id]').val(rowdata.ID);
				$('input[name=des_name]').val(rowdata.Description);

				var str_menu_desktop = rowdata.PermissionDesktop;
				var str_menu_mobile = rowdata.PermissionMobile;
				var str_default_desktop = rowdata.DefaultDesktop;
				var str_default_mobile = rowdata.DefaultMobile;
				var setting_actions = rowdata.actions;

				if (!!str_menu_desktop) {
					var tmp_menu_desktop = str_menu_desktop.split(',');
				}

				if (!!str_menu_mobile) {
					var tmp_menu_mobile = str_menu_mobile.split(',');
				}

				if (!!setting_actions) {
					var tmp_actions = setting_actions.split(',');
				}

				var cast_menu_desktop = [];
				var cast_menu_mobile = [];
				var user_actions_active = [];

				if (!!tmp_menu_desktop) {
					// Cast str to int
					$.each(tmp_menu_desktop, function(index, val) {
						// store array
						 cast_menu_desktop.push(parseInt(val));
					});
				}

				if (!!tmp_menu_mobile) {
					// Cast str to int
					$.each(tmp_menu_mobile, function(index, val) {
						// store array
						 cast_menu_mobile.push(parseInt(val));
					});
				}

				gojax('get', base_url+'/api/menu/all')
					.done(function(data) {

						$('#permission_desktop').html("");
						
						$.each(data, function(k, v) {
							// Desktop
							if (v.Link !== '?show=0') {
								if ($.inArray(v.ID, cast_menu_desktop) === -1) {
									$('#permission_desktop').append('<option value="'+ v.ID +'">'+v.Description+'</option>');
								} else {
									$('#permission_desktop').append('<option value="'+ v.ID +'" selected>'+v.Description+'</option>');
								}
							}
						});

					$('#permission_desktop').multipleSelect({
						placeholder: 'เลือกข้อมูล', 
						filter: true,
						onUncheckAll: function() {
							$("#default_page_desktop").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							// $("#user_actions").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: false});
						},
						onCheckAll: function() {

	          	gojax('get', base_url + '/api/menu/all')
								.done(function(data) {
									$('#default_page_desktop').html('');
									$.each(data, function(index, val) {
										if (val.Link !== '?show=0') {
											$('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
										}
									});
									$('select.select_single').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
								});

							// gojax('get', base_url+'/api/actions')
							// 	.done(function(data) {
							// 		$('#user_actions').html('');
							// 		$.each(data, function(k, v) {
							// 			$('#user_actions').append('<option value="'+ v.id +'">'+v.description+'</option>');
							// 		});
							// 		$('#user_actions').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
							// 	});
	          },
						onClick: function(view) {
							permission_desktop_selected = $('#permission_desktop').multipleSelect('getSelects');
							
							gojax('get', base_url + '/api/menu/all')
								.done(function(data) {
									$('#default_page_desktop').html('');
									$.each(data, function(index, val) {
										if (val.Link !== '?show=0') {
											if ($.inArray(val.ID.toString(), permission_desktop_selected) !== -1) {
												$('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
											}
										}
									});
									$('#default_page_desktop').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
								});

							if (permission_desktop_selected.length !== 0) {
								// gojax('get', base_url+'/api/actions')
								// 	.done(function(data) {
								// 		$('#user_actions').html('');
								// 		$.each(data, function(k, v) {
								// 			if ($.inArray(v.menu_id.toString(), permission_desktop_selected) !== -1) {
								// 				$('#user_actions').append('<option value="'+ v.id +'">'+v.description+'</option>');
								// 			}
								// 		});
								// 		$('#user_actions').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
								// 	});
							} else {
								$('#user_actions').html('').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
							}
						}
					});

					permission_desktop_selected = $('#permission_desktop').multipleSelect('getSelects');

					if (permission_desktop_selected.length !== 0) {
						gojax('get', base_url+'/api/actions')
							.done(function(data) {
								$('#user_actions').html('');
								$.each(data, function(k, v) {
									if ($.inArray(v.menu_id.toString(), permission_desktop_selected) !== -1) {
										if ($.inArray(v.id.toString(), tmp_actions) !== -1) {
											$('#user_actions').append('<option value="'+ v.id +'" selected>'+v.description+'</option>');
										} else {
											$('#user_actions').append('<option value="'+ v.id +'">'+v.description+'</option>');
										}
										
									}
								});
								$('#user_actions').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
							});
					} else {
						$('#user_actions').html('').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
					}
				});

				gojax('get', base_url+'/api/menu/all')
					.done(function(data) {
						$('#permission_mobile').html("");
						$.each(data, function(k, v) {
							// Mobile
							if (v.Link !== '?show=0') {
								if ($.inArray(v.ID, cast_menu_mobile) === -1) {
									$('#permission_mobile').append('<option value="'+ v.ID +'">'+v.Description+'</option>');
								} else {
									$('#permission_mobile').append('<option value="'+ v.ID +'" selected>'+v.Description+'</option>');
								}	
							}			
						});

						$('#permission_mobile').multipleSelect({
							placeholder: 'เลือกข้อมูล', 
							filter: true,
							onUncheckAll: function() {
								$("#default_page_mobile").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							},
							onCheckAll: function() {

								gojax('get', base_url + '/api/menu/all')
									.done(function(data) {
										$.each(data, function(index, val) {
											if (val.Link !== '?show=0') {
												$('#default_page_mobile').append('<option value="'+val.ID+'">'+val.Description+'</option>');
											}
										});
										$('#default_page_mobile').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
									});
							},
							onClick: function() {

								permission_mobile_selected = $('#permission_mobile').multipleSelect('getSelects');
								
								gojax('get', base_url + '/api/menu/all')
									.done(function(data) {
										$('#default_page_mobile').html('');
										$.each(data, function(index, val) {
											if (val.Link !== '?show=0') {
												if ($.inArray(val.ID.toString(), permission_mobile_selected) !== -1) {
													$('#default_page_mobile').append('<option value="'+val.ID+'">'+val.Description+'</option>');
												}
											}
										});
										$('#default_page_mobile').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
									});
							}
						});
					});

				gojax('get', base_url+'/api/menu/all')
					.done(function(data) {
						$('#default_page_desktop').html('');
						$.each(data, function(index, val) {
							if (val.Link !== '?show=0') {
								$('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
							}
						});
						$('#default_page_desktop')
							.val(str_default_desktop)
							.multipleSelect({
								placeholder: 'เลือกข้อมูล', 
								single: true
							});
					});

				gojax('get', base_url+'/api/menu/all')
					.done(function(data) {
						$('#default_page_mobile').html('');
						$.each(data, function(index, val) {
							if (val.Link !== '?show=0') {
								$('#default_page_mobile').append('<option value="'+val.ID+'">'+val.Description+'</option>');
							}
						});
						$('#default_page_mobile')
							.val(str_default_mobile)
							.multipleSelect({
								placeholder: 'เลือกข้อมูล', 
								single: true
							});
					});

				if (rowdata.Status==1){
	        $('input[name=status]').prop('checked' , true);
	      }else if(rowdata.Status==0){
	        $('input[name=status]').prop('checked' , false);
	      }
			}
		});

	});

	function modal_create_open() {

		$('#modal_per_create').modal({backdrop: 'static'});
		$('#form_create_per').trigger('reset');
		$('.modal-title').text('Create new');
		$('input[name=form_type]').val('create');

		$("#default_page_desktop").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
		$("#default_page_mobile").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
		$("#user_actions").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: false});

		// desktop
		gojax('get', base_url+'/api/menu/all')
			.done(function(data) {
				$('#permission_desktop').html("");
				$.each(data, function(k, v) {
					// Desktop
					if (v.Link !== '?show=0') {
						$('#permission_desktop').append('<option value="'+ v.ID +'">'+v.Description+'</option>');
					}
				});

				// update when select 
				$('#permission_desktop').multipleSelect({
					placeholder: 'เลือกข้อมูล', 
					filter: true,
					onUncheckAll: function() {
						$("#default_page_desktop").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
						// $("#user_actions").html('').multipleSelect({placeholder: 'เลือกข้อมูล', single: false});
					},
					onCheckAll: function() {

          	gojax('get', base_url + '/api/menu/all')
							.done(function(data) {
								$('#default_page_desktop').html('');
								$.each(data, function(index, val) {
									// console.log(data.Link);
									if (val.Link !== '?show=0') {
										$('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
									}
								});
								$('#default_page_desktop').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							});

						// gojax('get', base_url+'/api/actions')
						// 	.done(function(data) {
						// 		$('#user_actions').html('');
						// 		$.each(data, function(k, v) {
						// 			$('#user_actions').append('<option value="'+ v.id +'">'+v.description+'</option>');
						// 		});
						// 		$('#user_actions').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
						// 	});
          },
					onClick: function(view) {

						permission_desktop_selected = $('#permission_desktop').multipleSelect('getSelects');

						gojax('get', base_url + '/api/menu/all')
							.done(function(data) {
								$('#default_page_desktop').html('');
								$.each(data, function(index, val) {
									if (val.Link !== '?show=0') {
										// $('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
										if ($.inArray(val.ID.toString(), permission_desktop_selected) !== -1) {
											$('#default_page_desktop').append('<option value="'+val.ID+'">'+val.Description+'</option>');
										}
									}
								});
								$('#default_page_desktop').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							});

						if (permission_desktop_selected.length !== 0) {
							// gojax('get', base_url+'/api/actions')
							// 	.done(function(data) {
							// 		$('#user_actions').html('');
							// 		$.each(data, function(k, v) {
							// 			if ($.inArray(v.menu_id.toString(), permission_desktop_selected) !== -1) {
							// 				$('#user_actions').append('<option value="'+ v.id +'">'+v.description+'</option>');
							// 			}
							// 		});
							// 		$('#user_actions').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
							// 	});
						} else {
							$('#user_actions').html('').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
						}
					}
				});
			});

		// mobile
		gojax('get', base_url+'/api/menu/all')
			.done(function(data) {
				$('#permission_mobile').html("");
				$.each(data, function(k, v) {
					// Mobile
					if (v.Link !== '?show=0') {
						$('#permission_mobile').append('<option value="'+ v.ID +'">'+v.Description+'</option>');
					}
				});
				
				// Update when select
				$('#permission_mobile').multipleSelect({
					placeholder: 'เลือกข้อมูล', 
					filter: true,
					onUncheckAll: function() {
						$("#default_page_mobile").html('');
						$('#default_page_mobile').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
					},
					onCheckAll: function() {

						gojax('get', base_url + '/api/menu/all')
							.done(function(data) {
								$.each(data, function(index, val) {
									if (val.Link !== '?show=0') {
										$('#default_page_mobile').append('<option value="'+val.ID+'">'+val.Description+'</option>');
									}
								});
								$('#default_page_mobile').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							});
					},
					onClick: function() {

						permission_mobile_selected = $('#permission_mobile').multipleSelect('getSelects');
						
						gojax('get', base_url + '/api/menu/all')
							.done(function(data) {
								$('#default_page_mobile').html('');
								$.each(data, function(index, val) {
									if (val.Link !== '?show=0') {
										if ($.inArray(val.ID.toString(), permission_mobile_selected) !== -1) {
											$('#default_page_mobile').append('<option value="'+val.ID+'">'+val.Description+'</option>');
										}
									}
								});
								$('#default_page_mobile').multipleSelect({placeholder: 'เลือกข้อมูล', single: true});
							});
					}
				});
			});
	}

	function submit_create_per() {
		var	des_name = $('input[name=des_name]').val();
		var	d = $('#permission_desktop').val();
		var	m = $('#permission_mobile').val();
		if (!!des_name) {
			$('button').prop('disabled', true);
			$.ajax({
				url : base_url + '/api/permission/create',
				type : 'post',
				cache : false,
				dataType : 'json',
				data : $('form#form_create_per').serialize()
			})
			.done(function(data) {
				if (data.status !== 200) {
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
				} else {
					$('#modal_per_create').modal('hide');
					$('#grid_per').jqxGrid('updatebounddata');
				}
				$('button').prop('disabled', false);
			});
		} else {
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณาใส่ข้อมูลให้ครบถ้วน');
		}

		return false;
	}

	function grid_per(){
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
	        datafields: [
	            { name: "ID", type: "int"},
	            { name: "Description", type: "string" },
	            { name: "PermissionDesktop", type: "string" },
	            { name: "PermissionMobile", type: "string" },
	            { name: 'DefaultDesktop', type: 'string'},
	            { name: 'DefaultMobile', type: 'string'},
	            { name: 'actions', type: 'string'},
	            { name: "Status", type:"int"}
	        ],
	        url : base_url+'/api/permission/all'
		});

		return $("#grid_per").jqxGrid({
        width: '100%',
        source: dataAdapter,
        // autorowheight: true,
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
          { text:"ID", datafield: "ID", width: 100},
          { text:"Description", datafield: "Description", width: 200},
          { text:"Desktop", datafield: "PermissionDesktop", width: 100},
          { text:"Mobile", datafield: "PermissionMobile", width: 100},
          { text:"Default Desktop", datafield: "DefaultDesktop", width: 150},
          { text:"Default Mobile", datafield: "DefaultMobile", width: 150},
          { text:"Actions", datafield: "actions", width: 150},
          { text:"Status", datafield: "Status", width: 100}        
          ]
	  });
	}
</script>