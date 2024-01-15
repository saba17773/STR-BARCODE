<?php $this->layout("layouts/base", ['title' => 'User']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>
<style>
    td {
        padding: 5px;
    }
</style>

<div class="head-space"></div>
<div class="panel panel-default">
    <div class="panel-heading">Send to Warehouse</div>
    <div class="panel-body">
        <div class="btn-panel">
            <!-- <#?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'create_user_management') === true) : ?> -->
            <button class="btn btn-primary" id="create">Create</button>
            <!-- <#?php endif ?> -->
            <!-- <#?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'edit_user_management') === true) : ?> -->
            <button class="btn btn-info" id="line">LINE</button>
            <!-- <#?php endif ?> -->
            <!-- <#?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'copy_user_management') === true) : ?> -->
            <button class="btn btn-warning" id="btn_print">Print <span id="text_user"></span></button>
            <!-- <#?php endif ?> -->

            <!-- <#?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'copy_user_management') === true) : ?> -->
            <button class="btn btn-success" id="btn_update">Complete <span id="text_user"></span></button>
            <!-- <#?php endif ?> -->

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

                            <td>Truck</td>
                            <td>
                                <select name="truck" id="truck" class="form-control" required></select>
                            </td>
                            <!-- <td>Authorize</td>
                            <td>
                               
                                <select name="round" id="round" class="form-control"></select>

                            </td> -->

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

<!-- Line Modal -->
<div class="modal" id="modal_line" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <!-- Content -->
                <div id="grid_line"></div>
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
        $('#btn_update').hide();

        var department = $('input[name=department]');
        var warehouse = $('select[name=warehouse]');
        var location = $('select[name=location]');
        var company = $('select[name=company]');
        var truck = $('select[name=truck]');

        var user_name = '<?php echo $_SESSION["user_name"]; ?>';

        grid_user();




        $('#btn_print').on('click', function() {
            var rowdata = row_selected('#grid_user');
            if (typeof rowdata !== 'undefined') {
                window.open(base_url + '/sendwhround/print/' + rowdata.JournalID + '?mode=1', '_blank');
            }
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


            gojax('get', base_url + '/api/press/alltruck')
                .done(function(data) {
                    $('select[name=truck]').html('<option value="">= เลือกข้อมูล =</option>');
                    $.each(data, function(index, val) {
                        $('select[name=truck]').append('<option value="' + val.PlateNumber + '">' + val.PlateNumber + '</option>');
                    });
                    // $('select._select').multipleSelect({placeholder:'เลือกข้อมูล'});
                });



            gojax('get', base_url + '/api/press/allround')
                .done(function(data) {
                    $('select[name=round]').html('<option value="">= เลือกข้อมูล =</option>');
                    $.each(data, function(index, val) {
                        $('select[name=round]').append('<option value="' + val.Description + '">' + val.Description + '</option>');
                    });
                    // $('#auth').val(4); // default to not authorize
                });






            // end
        });

        $('#line').on('click', function() {

            var rowdata = row_selected('#grid_user');
            if (typeof rowdata !== 'undefined') {
                $('.modal-title').text(rowdata.JournalID);
                $('#modal_line').modal({
                    backdrop: 'static'
                });
                // $('.modal-title').text(rowdata.InventJournalID);
                grid_line(rowdata.JournalID);
            } else {
                alert('กรุณาเลือกรายการ');
            }

        });
    });

    $('#grid_user').on('rowclick', function(event) {
        var args = event.args;
        var boundIndex = args.rowindex;
        var datarow = $("#grid_user").jqxGrid('getrowdata', boundIndex);
        // console.log(datarow.Status);
        if (datarow.Complete === 0) {
            $('#btn_update').show();
        } else {
            $('#btn_update').hide();
        }



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
                    url: base_url + '/api/warehousesendtable/create',
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


    $('#btn_update').on('click', function(event) {
        event.preventDefault();
        var rowdata = row_selected('#grid_user');
        $('input[name=form_type]').val('update');
        $('input[name=_id]').val(rowdata.Id);
        // alert(rowdata.Id);
        // exit();
        if (!!rowdata) {
            if (confirm('Are you sure?')) {
                $.ajax({
                        url: base_url + '/api/warehousesendtable/create',
                        type: 'post',
                        cache: false,
                        dataType: 'json',
                        data: $('form#form_user').serialize()
                    })
                    .done(function(data) {
                        if (data.status == 200) {

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
        }
    });




    function grid_user() {
        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [{
                    name: 'Id',
                    type: 'number'
                },
                {
                    name: 'JournalID',
                    type: 'string'
                },
                {
                    name: 'JournalDescription',
                    type: 'string'
                },
                {
                    name: 'TruckID',
                    type: 'string'
                },
                {
                    name: 'CreateDate',
                    type: 'string'
                },
                {
                    name: 'Complete',
                    type: 'number'
                },
                {
                    name: 'CompleteDate',
                    type: 'string'
                },
                {
                    name: 'Count',
                    type: 'number'
                }

            ],
            url: base_url + '/api/whsendwarehouse/all'
        });

        return $("#grid_user").jqxGrid({
            width: '98%',
            source: dataAdapter,
            autoheight: true,
            pageSize: 10,
            altrows: true,
            pageable: true,
            sortable: true,
            filterable: true,
            showfilterrow: true,
            columnsresize: true,
            columns: [{
                    text: 'Journal ID',
                    datafield: 'JournalID',
                    width: 200
                },
                {
                    text: 'Round',
                    datafield: 'TruckID',
                    width: 200
                },
                {
                    text: 'Description',
                    datafield: 'JournalDescription',
                    width: 200
                },

                {
                    text: 'Count',
                    datafield: 'Count',
                    width: 200
                },
                {
                    text: 'Create Date',
                    datafield: 'CreateDate',
                    width: 200
                },
                {
                    text: 'Complete',
                    datafield: 'Complete',
                    width: 130,
                    columntype: 'checkbox',
                    filtertype: 'bool'
                },
                {
                    text: 'Complete Date',
                    datafield: 'CompleteDate',
                    width: 200
                }
            ]
        });
    }

    function grid_line($id) {

        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [{
                    name: 'JournalID',
                    type: 'string'
                },
                {
                    name: 'Barcode',
                    type: 'string'
                },
                {
                    name: 'ItemID',
                    type: 'string'
                },
                {
                    name: 'NameTH',
                    type: 'string'
                },
                {
                    name: 'ID',
                    type: 'string'
                },
                {
                    name: 'Batch',
                    type: 'string'
                },
                {
                    name: 'CreateDate',
                    type: 'string'
                }

            ],
            url: base_url + '/api/whsendwarehouse/' + $id + '/allline'
        });

        return $("#grid_line").jqxGrid({
            width: '98%',
            source: dataAdapter,
            autoheight: true,
            pageSize: 10,
            altrows: true,
            pageable: true,
            sortable: true,
            filterable: true,
            showfilterrow: true,
            columnsresize: true,
            columns: [{
                    text: 'Barcode',
                    datafield: 'Barcode',
                    width: 200
                },
                {
                    text: 'Item',
                    datafield: 'ItemID',
                    width: 200
                },
                {
                    text: 'Item Name',
                    datafield: 'NameTH',
                    width: 200
                },
                {
                    text: 'Curecode',
                    datafield: 'ID',
                    width: 200
                },
                {
                    text: 'Batch',
                    datafield: 'Batch',
                    width: 200
                },

                {
                    text: 'Create Date',
                    datafield: 'CreateDate',
                    width: 200
                }
            ]
        });
    }
</script>