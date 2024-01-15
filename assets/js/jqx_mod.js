function set_window(id, width, height) {
	return $(id).jqxWindow({
		autoOpen : false,
		isModal : true,
		width : width,
		height : height,
		animationType: 'none'
	});
}

function call_window(id, event) {
	return $(id).jqxWindow(event);
}

function row_selected(grid_name) {
    var selectedrowindex = $(grid_name).jqxGrid('getselectedrowindex');
    var datarow = $(grid_name).jqxGrid('getrowdata', selectedrowindex);
    return datarow;
}

function close_submit() {
	$('button[type="submit"]').prop('disabled', true);
	$('button[type="reset"]').prop('disabled', true);
}

function open_submit() {
	$('button[type="submit"]').prop('disabled', false);
	$('button[type="reset"]').prop('disabled', false);
}

function close_button() {
	$('button').prop('disabled', true);
	$('button').prop('disabled', true);
}

function open_button() {
	$('button').prop('disabled', false);
	$('button').prop('disabled', false);
}

function form_reset(form_id) {
	return $(form_id).trigger('reset');
}

function jqx_menu(selector) {
	$(selector).jqxMenu({
		showTopLevelArrows: true
    });
}