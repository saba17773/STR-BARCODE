<?php $this->layout("layouts/base", ['title' => 'User']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<style>
	td {
		padding: 5px;
	}
</style>

<div class="head-space"></div>
<div class="panel panel-default">
	<div class="panel-heading">User Management</div>
	<div class="panel-body">
		<div class="btn-panel">
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'create_user_management') === true) : ?>
				<button class="btn btn-primary" id="create">Create new user</button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'edit_user_management') === true) : ?>
				<button class="btn btn-info" id="edit">Edit user infomation</button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'copy_user_management') === true) : ?>
				<button class="btn btn-warning" id="copy_user">Copy User <span id="text_user"></span></button>
			<?php endif ?>
			<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'print_barcode_user_management') === true) : ?>
				<button class="btn btn-default" id="print">Print Barcode <span id="text_print"></span></button>
			<?php endif ?>
		</div>
		<div id="grid_user"></div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Create new</h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<form id="form_user" onsubmit="return form_user_submit()" style="overflow: hidden;">
					<table>
						<tr>
							<td valign="center">Username</td>
							<td>
								<input type="text" name="username" class="form-control" required>
							</td>
							<td>Password</td>
							<td>
								<input type="password" name="password" class="form-control" required>
							</td>
						</tr>
						<tr>
							<td>Name</td>
							<td>
								<input type="text" name="fullname" class="form-control" required readonly>
							</td>
							<td>Department</td>
							<td>
								<!-- <select name="department" class="form-control" required readonly></select> -->
								<input type="text" name="department_desc" class="form-control" required readonly>
								<input type="hidden" name="department" class="form-control" required>
								<input type="hidden" name="department_desc_text" value="all">
							</td>
						</tr>
						<tr>
							<td>Warehouse</td>
							<td>
								<select name="warehouse" class="form-control" required></select>
							</td>
							<td>Location</td>
							<td>
								<select name="location" class="form-control" required></select>
							</td>
						</tr>
						<tr>
							<td>Company</td>
							<td>
								<select name="company" id="company" class="form-control" required></select>
							</td>
							<td>Employee ID</td>
							<td>
								<div class="input-group">
									<input type="text" name="empid" class="form-control" readonly />
									<span class="input-group-btn">
										<button class="btn btn-info" type="button" onclick="return grid_select_emp()" data-toggle="modal" data-target="#modal_select_emp"><span class="glyphicon glyphicon-search"></span></button>
									</span>
								</div><!-- /input-group -->
							</td>
						</tr>
						<tr>

							<td>Permission Group</td>
							<td>
								<select name="permission" class="form-control" required></select>
							</td>
							<td>Authorize</td>
							<td>
								<!-- <input type="checkbox" id="auth" name="auth"> -->
								<select name="auth" id="auth" class="form-control"></select>
							</td>

						</tr>
						<tr>
							<!-- <td>
								Default Page Desktop
							</td>
							<td>
								<select name="default_page" id="default_page" class="form-control"></select>
							</td>
							 -->
							<td>Status</td>
							<td>
								<?php if ($_SESSION["user_name"] === "admin") { ?>
									<input type="checkbox" id="status" name="status" checked>
								<?php } else { ?>
									<input type="checkbox" id="status" name="status" checked onclick="return false" onkeydown="return false">
								<?php } ?>
							</td>
							<td>
								Shift
							</td>
							<td>
								<select name="shift" id="shift" class="form-control"></select>
							</td>
						</tr>
						<tr>
							<td>
								Set Time Check
							</td>
							<td>
								<input type="checkbox" id="time_check" name="time_check">
							</td>
							<td>Component</td>
							<td>
								<input type="checkbox" id="component" name="component">
							</td>
						</tr>
						<tr>
							<td>
								<div id="divunit_name">
									Unit Component
								</div>
							</td>
							<td>
								<div id="divunit_select">
									<select name="unit" id="unit" class="form-control"></select>
								</div>
							</td>
							<td>
								<div id="divsec_name">
									Section Component
								</div>
							</td>
							<td>
								<div id="div_section">
									<!-- <select name="section" id="section" class="form-control"></select> -->
									<select name="section[]" id="section" multiple="multiple" style="width: 270px;"></select>
								</div>
							</td>
						</tr>
					</table>
					<input type="hidden" name="form_type">
					<input type="hidden" name="_id">
					<button type="submit" class="btn btn-primary pull-right">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_select_emp" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Select Employee</h4>
			</div>
			<div class="modal-body">
				<!-- Content -->
				<div id="grid_employee"></div>
			</div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#divunit_name').hide();
		$('#divunit_select').hide();
		$('#divsec_name').hide();
		$('#div_section').hide();

		var department = $('input[name=department]');
		var warehouse = $('select[name=warehouse]');
		var location = $('select[name=location]');
		var company = $('select[name=company]');
		var permission = $('select[name=permission]');

		var user_name = '<?php echo $_SESSION["user_name"]; ?>';

		grid_user();

		$('#copy_user').on('click', function() {
			var rowdata = row_selected('#grid_user');
			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({
					backdrop: 'static'
				});
				$('input[name=username]').val('');
				$('input[name=password]').prop('readonly', false).val('');
				$('input[name=fullname]').val('');
				$('input[name=department]').val('');
				$('input[name=department_desc]').val('');
				$('input[name=department_desc_text]').val(rowdata.DepartmentDesc);
				$('input[name=empid]').val('');
				$('input[name=form_type]').val('create');
				$('input[name=username]').prop('readonly', false);
				$('.modal-title').text('Copy user : ' + rowdata.Name);

				if (rowdata.Status == 1) {
					$('#status').prop('checked', true);
				} else {
					$('#status').prop('checked', false);
				}

				if (rowdata.SkipingDelay == 1) {
					$('#time_check').prop('checked', true);
				} else {
					$('#time_check').prop('checked', false);
				}

				$('#status').on('click', function(event) {
					event.preventDefault();
				});

				// if (rowdata.Authorize == 1) {
				// 	 $('#auth').prop('checked' , true);
				// } else {
				// 	 $('#auth').prop('checked' , false);
				// }	

				gojax('get', base_url + '/api/warehouse/all')
					.done(function(data) {
						warehouse.html('');
						$.each(data, function(index, val) {
							if (val.ID === rowdata.Warehouse) {
								warehouse.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							}
						});
					});

				gojax('get', base_url + '/api/location/all')
					.done(function(data) {
						location.html('');
						$.each(data, function(index, val) {
							if (val.ID === rowdata.Location) {
								location.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							}
						});
					});

				gojax('get', base_url + '/api/company/all')
					.done(function(data) {
						company.html('');
						$.each(data, function(index, val) {
							if (val.ID === rowdata.Company) {
								company.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							}
						});
					});

				gojax('get', base_url + '/api/permission/all')
					.done(function(data) {
						permission.html('');
						$.each(data, function(index, val) {
							if (val.ID === rowdata.PermissionID) {
								permission.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							}
						});
					});

				gojax('get', base_url + '/api/shift/all')
					.done(function(data) {
						$('select[name=shift]').html('');
						$.each(data, function(index, val) {
							$('select[name=shift]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
						});
						$('select[name=shift]').val(rowdata.Shift);
					});

				gojax('get', base_url + '/api/authorize/all')
					.done(function(data) {
						$('#auth').html('');
						$.each(data, function(index, val) {
							if (val.ID === rowdata.Authorize) {
								$('#auth').append('<option value="' + val.ID + '">' + val.Description + '</option>');
							}
						});
					});
				// end
			}
		});

		$('#grid_user').on('rowselect', function(event) {
			var args = event.args;
			var rowBoundIndex = args.rowindex;
			var rowdata = args.row;

			if (typeof rowdata !== 'undefined') {
				$('#text_user').text('').append(' => [' + rowdata.Name + ']');
				$('#text_print').text('').append(' => [' + rowdata.Name + ']');
			}
		});

		$('#print').on('click', function(event) {
			event.preventDefault();
			var rowdata = row_selected('#grid_user');
			window.open(base_url + '/generator/user/' + rowdata.Username + '/' + rowdata.EmployeeID + '/' + $.base64.encode(rowdata.Password) + '/' + rowdata.Name, '_blank');
		});

		$('#edit').on('click', function(e) {

			var rowdata = row_selected('#grid_user');

			if (typeof rowdata !== 'undefined') {
				$('#modal_create').modal({
					backdrop: 'static'
				});
				$('input[name=username]').val(rowdata.Username);
				$('input[name=password]').val(rowdata.Password);
				$('input[name=fullname]').val(rowdata.Name);
				$('input[name=department]').val(rowdata.Department);
				$('input[name=department_desc]').val(rowdata.DepartmentDesc);
				$('input[name=department_desc_text]').val(rowdata.DepartmentDesc);
				$('input[name=form_type]').val('update');
				$('input[name=username]').prop('readonly', true);
				$('.modal-title').text('Update');
				$('input[name=_id]').val(rowdata.ID);

				var str_menu = rowdata.SectionComponent;

				if (!!str_menu) {
					var tmp_menu = str_menu.split(",");
				}

				var cast_menu = [];
				// Cast str to int
				$.each(tmp_menu, function(index, val) {
					// store array
					cast_menu.push(parseInt(val));
				});

				$('#section').html("");

				getSectionComponent()
					.done(function(data) {
						$.each(data, function(k, v) {
							if ($.inArray(v.SectionID, cast_menu) === -1) {
								$('#section').append('<option value="' + v.SectionID + '">' + v.SectionName + '</option>');
							} else {
								$('#section').append('<option value="' + v.SectionID + '" selected>' + v.SectionName + '</option>');
							}
							$('#section').multipleSelect({
								placeholder: 'เลือกข้อมูล',
								filter: true,
								position: 'top'
							});

						});
					});

				// edit 3/2/2017
				// $('select[name=warehouse]').prop('readonly', true);

				if (user_name !== rowdata.Username) {
					if (user_name === "admin") {
						$('input[name=password]').prop('readonly', false);
					} else {
						$('input[name=password]').prop('readonly', true);
					}
				} else {
					$('input[name=password]').prop('readonly', false);
				}

				if (rowdata.Status == 1) {
					$('#status').prop('checked', true);
				} else {
					$('#status').prop('checked', false);
				}

				if (rowdata.SkipingDelay == 1) {
					$('#time_check').prop('checked', true);
				} else {
					$('#time_check').prop('checked', false);
				}

				// if (rowdata.Authorize == 1) {
				// 	 $('#auth').prop('checked' , true);
				// } else {
				// 	 $('#auth').prop('checked' , false);
				// }

				if (rowdata.UnitComponent != undefined) {
					$('#component').prop('checked', true);
					$('#divunit_name').show();
					$('#divunit_select').show();
					$('#divsec_name').show();
					$('#div_section').show();
				} else {
					$('#component').prop('checked', false);
					$('#divunit_name').hide();
					$('#divunit_select').hide();
					$('#divsec_name').hide();
					$('#div_section').hide();
				}

				$("#component").on('change', function() {

					if ($(this).is(':checked')) {
						$(this).attr('value', 'true');
						$('#divunit_name').show();
						$('#divunit_select').show();
						$('#divsec_name').show();
						$('#div_section').show();
					} else {
						$(this).attr('value', 'false');
						$('#divunit_name').hide();
						$('#divunit_select').hide();
						$('#divsec_name').hide();
						$('#div_section').hide();
					}

				});

				gojax('get', base_url + '/api/warehouse/all')
					.done(function(data) {
						warehouse.html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								warehouse.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.Warehouse === val.ID) {
									warehouse.append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						warehouse.val(rowdata.Warehouse);
					});

				gojax('get', base_url + '/api/location/all')
					.done(function(data) {
						location.html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								location.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.Location === val.ID) {
									location.append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						location.val(rowdata.Location);
					});

				gojax('get', base_url + '/api/company/all')
					.done(function(data) {
						company.html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								company.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.Company === val.ID) {
									company.append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						company.val(rowdata.Company);
					});

				gojax('get', base_url + '/api/permission/all')
					.done(function(data) {
						permission.html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								permission.append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.PermissionID === val.ID) {
									permission.append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						permission.val(rowdata.PermissionID);
					});

				gojax('get', base_url + '/api/shift/all')
					.done(function(data) {
						$('select[name=shift]').html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								$('select[name=shift]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.Shift === val.ID) {
									$('select[name=shift]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						$('select[name=shift]').val(rowdata.Shift);
					});

				gojax('get', base_url + '/api/authorize/all')
					.done(function(data) {
						$('#auth').html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								$('#auth').append('<option value="' + val.ID + '">' + val.Description + '</option>');
							} else {
								if (rowdata.Authorize === val.ID) {
									$('#auth').append('<option value="' + val.ID + '">' + val.Description + '</option>');
								}
							}
						});
						$('#auth').val(rowdata.Authorize);
					});

				gojax('get', base_url + '/component/unit')
					.done(function(data) {
						$('select[name=unit]').html('');
						$.each(data, function(index, val) {
							if (user_name === "admin") {
								$('select[name=unit]').append('<option value="' + val.ID + '">' + val.DepartmentUnit + '</option>');
							} else {
								if (rowdata.UnitComponent === val.ID) {
									$('select[name=unit]').append('<option value="' + val.ID + '">' + val.DepartmentUnit + '</option>');
								}
							}
						});
						$('select[name=unit]').val(rowdata.UnitComponent);
					});

				// gojax('get', base_url + '/component/section')
				// 	.done(function(data) {
				// 		$('select[name=section]').html('');
				// 		$.each(data, function(index, val) {
				// 			if (user_name === "admin") {
				// 				$('select[name=section]').append('<option value="'+val.SectionID+'">'+val.SectionName+'</option>');
				// 			} else {
				// 				if (rowdata.SectionComponent === val.SectionID) {
				// 					$('select[name=section]').append('<option value="'+val.SectionID+'">'+val.SectionName+'</option>');
				// 				}
				// 			}
				// 		});		
				// 		$('select[name=section]').val(rowdata.SectionComponent);
				// 	});

				$('input[name=empid]').val(rowdata.EmployeeID);
			} else {
				alert("ไม่สามารถแก้ไขรายการนี้ได้");
			}
		});

		$('#grid_employee').on('rowdoubleclick', function(event) {
			var rowdata = row_selected('#grid_employee');
			var department_text = '-';

			gojax('get', base_url + '/api/department/all')
				.done(function(data) {
					$.each(data, function(index, val) {
						if (val.Code === rowdata.DepartmentCode) {
							department_text = val.Description;
							$('input[name=empid]').val(rowdata.Code);
							$('input[name=fullname]').val(rowdata.FirstName + ' ' + rowdata.LastName);
							$('input[name=department_desc]').val(department_text);
							$('input[name=department]').val(rowdata.DepartmentCode);
							$('#modal_select_emp').modal('hide');
						}
					});
				});
		});

		warehouse.on('change', function(event) {
			event.preventDefault();
			/* Act on the event */
			var wh_id = warehouse.val();

			gojax('get', base_url + '/api/location/by_warehouse/' + wh_id)
				.done(function(data) {
					location.html('');
					$.each(data, function(index, val) {
						location.append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
				});
		});

		$('#create').on('click', function() {
			$('#modal_create').modal({
				backdrop: 'static'
			});
			$('form#form_user').trigger('reset');
			$('input[name=form_type]').val('create');
			$('.modal-title').text('Create new');
			$('input[name=username]').prop('readonly', false);

			$('#section').html("");
			getSectionComponent()
				.done(function(data) {
					$.each(data, function(k, v) {
						$('#section').append('<option value="' + v.SectionID + '">' + v.SectionName + '</option>');
					});
					$('#section').multipleSelect({
						placeholder: 'เลือกข้อมูล',
						filter: true,
						position: 'top'
					});
				});

			gojax('get', base_url + '/api/warehouse/all')
				.done(function(data) {
					$('select[name=warehouse]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=warehouse]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
				});

			gojax('get', base_url + '/api/location/all')
				.done(function(data) {
					$('select[name=location]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=location]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
				});

			gojax('get', base_url + '/api/company/all')
				.done(function(data) {
					$('select[name=company]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=company]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
					$('select[name=company]').val('STR');
				});

			gojax('get', base_url + '/api/permission/all')
				.done(function(data) {
					$('select[name=permission]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=permission]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
					// $('select._select').multipleSelect({placeholder:'เลือกข้อมูล'});
				});

			gojax('get', base_url + '/api/shift/all')
				.done(function(data) {
					// console.log(data);
					$('select[name=shift]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=shift]').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
				});

			gojax('get', base_url + '/api/authorize/all')
				.done(function(data) {
					$('#auth').html('<option value="">= กรุณาเลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('#auth').append('<option value="' + val.ID + '">' + val.Description + '</option>');
					});
					$('#auth').val(4); // default to not authorize
				});

			gojax('get', base_url + '/component/unit')
				.done(function(data) {
					$('select[name=unit]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=unit]').append('<option value="' + val.ID + '">' + val.DepartmentUnit + '</option>');
					});
				});

			gojax('get', base_url + '/component/section')
				.done(function(data) {
					$('select[name=section]').html('<option value="">= เลือกข้อมูล =</option>');
					$.each(data, function(index, val) {
						$('select[name=section]').append('<option value="' + val.SectionID + '">' + val.SectionName + '</option>');
					});
				});


			$("#component").on('change', function() {

				if ($(this).is(':checked')) {
					$(this).attr('value', 'true');
					$('#divunit_name').show();
					$('#divunit_select').show();
					$('#divsec_name').show();
					$('#div_section').show();
				} else {
					$(this).attr('value', 'false');
					$('#divunit_name').hide();
					$('#divunit_select').hide();
					$('#divsec_name').hide();
					$('#div_section').hide();
				}

			});
			// end
		});
	});

	function getSectionComponent() {
		return $.ajax({
			url: base_url + '/component/section',
			type: 'get',
			dataType: 'json',
			cache: false
		});
	}

	function form_user_submit() {
		if (confirm('Are you sure ?')) {
			close_button();
			$.ajax({
					url: base_url + '/api/user/create',
					type: 'post',
					cache: false,
					dataType: 'json',
					data: $('form#form_user').serialize()
				})
				.done(function(data) {
					if (data.status == 200) {
						;
						$('#modal_create').modal('hide');
						$('#grid_user').jqxGrid('updatebounddata');
					} else {
						alert(data.message);
					}
					open_button();
				})
				.fail(function() {
					alert('ไม่สามารถเชื่อมต่อเครือข่ายได้');
					open_button();
				});
		}
		return false;
	}

	function grid_select_emp() {
		grid_employee($.trim($('input[name=department_desc_text]').val()));
	}

	function grid_user() {
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'ID',
					type: 'number'
				},
				{
					name: 'Username',
					type: 'string'
				},
				{
					name: 'Password',
					type: 'string'
				},
				{
					name: 'Name',
					type: 'string'
				},
				{
					name: 'Department',
					type: 'string'
				},
				{
					name: 'DepartmentDesc',
					type: 'string'
				},
				{
					name: 'Authorize',
					type: 'number'
				},
				{
					name: 'AuthorizeDesc',
					type: 'string'
				},
				{
					name: 'Warehouse',
					type: 'number'
				},
				{
					name: 'WarehouseDesc',
					type: 'string'
				},
				{
					name: 'Location',
					type: 'number'
				},
				{
					name: 'LocationDesc',
					type: 'string'
				},
				{
					name: 'EmployeeID',
					type: 'string'
				},
				{
					name: 'Barcode',
					type: 'string'
				},
				{
					name: 'Shift',
					type: 'number'
				},
				{
					name: 'ShiftDesc',
					type: 'string'
				},
				{
					name: 'Status',
					type: 'bool'
				},
				{
					name: 'PermissionID',
					type: 'number'
				},
				{
					name: 'PermissionDesc',
					type: 'string'
				},
				{
					name: 'Company',
					type: 'string'
				},
				{
					name: 'DirectTo',
					type: 'number'
				},
				{
					name: 'DirectToMobile',
					type: 'number'
				},
				{
					name: 'SkipingDelay',
					type: 'bool'
				},
				{
					name: 'UnitComponent',
					type: 'number'
				},
				{
					name: 'SectionComponent',
					type: 'string'
				}
			],
			url: base_url + "/api/user/all"
		});

		return $("#grid_user").jqxGrid({
			width: '98%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			// rowsheight : 40,
			// columnsheight : 40,
			altrows: true,
			pageable: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme : 'theme',
			columns: [{
					text: 'Username',
					datafield: 'Username',
					width: 100
				},
				// { text: 'Password', datafield: 'Password', width: 100},
				{
					text: 'EmployeeID',
					datafield: 'EmployeeID',
					width: 100
				},
				{
					text: 'Barcode',
					datafield: 'Barcode',
					width: 100
				},
				{
					text: 'BarcodePass',
					width: 200,
					cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
						return '<div style=\'padding: 5px; color:#000000;\'> ' + rowdata.Barcode + rowdata.EmployeeID + ' </div>';
					}
				},
				{
					text: 'Name',
					datafield: 'Name',
					width: 200
				},
				{
					text: 'Department',
					datafield: 'DepartmentDesc',
					width: 200
				},
				{
					text: 'Authorize',
					datafield: 'AuthorizeDesc',
					width: 150
				},
				{
					text: 'Warehouse',
					datafield: 'WarehouseDesc',
					width: 200
				},
				{
					text: 'Location',
					datafield: 'LocationDesc',
					width: 200
				},
				{
					text: 'Shift',
					datafield: 'ShiftDesc',
					width: 100
				},
				{
					text: 'Time Lock Build',
					datafield: 'SkipingDelay',
					width: 130,
					columntype: 'checkbox',
					filtertype: 'bool'
				},
				{
					text: 'Status',
					datafield: 'Status',
					width: 100,
					columntype: 'checkbox',
					filtertype: 'bool'
				},
				{
					text: 'PermissionID',
					datafield: 'PermissionDesc',
					width: 200
				},
				{
					text: 'Company',
					datafield: 'Company',
					width: 100
				}
			]
		});
	}

	function grid_employee(department_desc_text) {
		var temp_dep_text = 'all';
		if (department_desc_text !== '' && department_desc_text !== null) {
			temp_dep_text = department_desc_text;
		}

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: 'json',
			datafields: [{
					name: 'Code',
					type: 'string'
				},
				{
					name: 'FirstName',
					type: 'string'
				},
				{
					name: 'LastName',
					type: 'string'
				},
				{
					name: 'DivisionCode',
					type: 'string'
				},
				{
					name: 'DepartmentCode',
					type: 'string'
				},
				{
					name: 'DivisionDesc',
					type: 'string'
				},
				{
					name: 'DepartmentDesc',
					type: 'string'
				}
			],
			url: base_url + "/api/employee/all/department/" + temp_dep_text
		});

		return $("#grid_employee").jqxGrid({
			width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize: 10,
			pageable: true,
			autoheight: true,
			sortable: true,
			filterable: true,
			showfilterrow: true,
			columnsresize: true,
			// theme : 'theme',
			columns: [{
					text: 'Code',
					datafield: 'Code',
					width: 150
				},
				{
					text: 'FirstName',
					datafield: 'FirstName',
					width: 150
				},
				{
					text: 'LastName',
					datafield: 'LastName',
					width: 150
				},
				{
					text: 'Division',
					datafield: 'DivisionDesc',
					width: 150
				},
				{
					text: 'Department',
					datafield: 'DepartmentDesc',
					width: 150
				}
			]
		});
	}
</script>