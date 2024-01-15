<?php $this->layout("layouts/base", ['title' => 'Set Check']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
    <div class="panel-heading">Set Check</div>
    <div class="panel-body">
        <div class="btn-panel">


            <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'edit_cure_tire_code') === true) : ?>
                <button class="btn btn-info" id="edit"><span class="glyphicon glyphicon-edit"></span> Edit</button>
            <?php endif ?>


        </div>

        <div id="grid_batch"></div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_create_batch" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Create new</h4>
            </div>
            <div class="modal-body">
                <form id="formcheck">

                    <div class="form-group">
                        <label for="check">Active</label>
                        <select name="_active" id="_active" class="form-control" required>
                            <option value="">Select data</option>
                            <option value="0">เปิดใช้งาน</option>
                            <option value="1">ปิดใช้งาน</option>
                           

                        </select>
                    </div>


                    

                    <input type="hidden" id="_id" name="_id">

                    <!-- <input type="hidden" name="form_type">
                    <input type="hidden" name="curetire_id"> -->

                    <label>
                        <button class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function($) {

        grid_batch();




        $('#edit').on('click', function(e) {
            var rowdata = row_selected("#grid_batch");
            // alert(rowdata);
            if (rowdata !== undefined) {
                $('#modal_create_batch').modal({
                    backdrop: 'static'
                });
                $('input[name=form_type]').val('update');
                $('input[name=_id]').val(rowdata.Type);
                $('select[name=_active]').val(rowdata.Active);
                $('.modal-title').text('Update');

            } else {
                alert("กรุณาเลือกรายการ");
            }
        });



        $("#formcheck").on("submit", function(e) {
            e.preventDefault();

            // var id_name = $('input[name=id_name]').val();
            // if (!!id_name) {

            $.ajax({
                    url: base_url + '/api/checkgreentire/update',
                    type: 'post',
                    cache: false,
                    dataType: 'json',
                    data: $('form#formcheck').serialize()
                })
                .done(function(data) {
                    if (data.result != true) {
                        //gotify(data.message, 'danger');
                        alert(data.message);
                    } else {
                        alert(data.message);
                        $('#modal_create_batch').modal('hide');
                        $('#grid_batch').jqxGrid('updatebounddata');
                    }
                });
            //  }
        });
    }); // end

    function modal_create_open() {
        $('#formcheck').trigger('reset');
        $('.modal-title').text('Create new');
        $('input[name=id_name]').prop('readonly', false);
        $('input[name=form_type]').val('create');
    }

    function grid_batch() {
        var dataAdapter = new $.jqx.dataAdapter({
            datatype: "json",
            datafields: [{
                    name: "Type",
                    type: "int"
                },
                {
                    name: "UpdatedBy",
                    type: "string"
                },
                {
                    name: "UpdatedDate",
                    type: "datetime"
                },
                {
                    name: "TypeName",
                    type: "string"
                },
                {
                    name: "Active",
                    type: "int"
                },
                {
                    name: "NameActive",
                    type: "string"
                }
            ],
            url: base_url + '/api/checkgreentire/all'
        });

        return $("#grid_batch").jqxGrid({
            width: '100%',
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
            // theme: 'theme',
            columns: [{
                    text: "Type",
                    datafield: "TypeName",
                    width: 100
                },
                {
                    text: "Date",
                    datafield: "UpdatedDate",
                    width: 200
                },
                {
                    text: "Active",
                    datafield: "NameActive",
                    width: 100
                }
            ]
        });
    }
</script>