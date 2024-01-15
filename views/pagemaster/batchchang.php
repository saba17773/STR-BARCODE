<?php $this->layout("layouts/base", ['title' => 'Curetire Code']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
    <div class="panel-heading">Set Start Batch</div>
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
                <form id="form_updatebatchweek">

                    <div class="form-group">
                        <label for="Date">Date</label>
                        <select name="Date" id="Date" class="form-control" required>
                            <option value="">Select data</option>
                            <option value="1">Sunday</option>
                            <option value="2">Monday</option>
                            <option value="3">Tuesday</option>
                            <option value="4">Wednesday</option>
                            <option value="5">Thursday</option>
                            <option value="6">Friday</option>
                            <option value="7">Saturday</option>

                        </select>
                    </div>


                    <div class="form-group">
                        <label for="timeset">timeset</label>
                        <select name="timeset" id="timeset" class="form-control" required>
                            <option value="">Select data</option>
                            <option value="06:00:00">06:00:00</option>
                            <option value="07:00:00">07:00:00</option>
                            <option value="08:00:00">08:00:00</option>
                            <option value="09:00:00">09:00:00</option>
                            <option value="10:00:00">10:00:00</option>
                            <option value="11:00:00">11:00:00</option>
                            <option value="12:00:00">12:00:00</option>
                            <option value="13:00:00">13:00:00</option>
                            <option value="14:00:00">14:00:00</option>
                            <option value="15:00:00">15:00:00</option>
                            <option value="16:00:00">16:00:00</option>
                            <option value="17:00:00">17:00:00</option>
                            <option value="18:00:00">18:00:00</option>
                            <option value="19:00:00">19:00:00</option>
                            <option value="20:00:00">20:00:00</option>
                            <option value="21:00:00">21:00:00</option>
                            <option value="22:00:00">22:00:00</option>
                            <option value="23:00:00">23:00:00</option>
                            <option value="00:00:00">24:00:00</option>
                            <option value="01:00:00">01:00:00</option>
                            <option value="02:00:00">02:00:00</option>
                            <option value="03:00:00">03:00:00</option>
                            <option value="04:00:00">04:00:00</option>
                            <option value="05:00:00">05:00:00</option>

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
                $('select[name=timeset]').val(rowdata.Time);
                $('select[name=Date]').val(rowdata.DateId);
                $('.modal-title').text('Update');

            } else {
                alert("กรุณาเลือกรายการ");
            }
        });



        $("#form_updatebatchweek").on("submit", function(e) {
            e.preventDefault();

            // var id_name = $('input[name=id_name]').val();
            // if (!!id_name) {

            $.ajax({
                    url: base_url + '/api/batch/updatebatch',
                    type: 'post',
                    cache: false,
                    dataType: 'json',
                    data: $('form#form_updatebatchweek').serialize()
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
        $('#form_updatebatchweek').trigger('reset');
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
                    name: "Date",
                    type: "string"
                },
                {
                    name: "Time",
                    type: "string"
                },
                {
                    name: "TypeName",
                    type: "string"
                },
                {
                    name: "DateId",
                    type: "int"
                }
            ],
            url: base_url + '/api/batch/changall'
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
                    datafield: "Date",
                    width: 200
                },
                {
                    text: "Time",
                    datafield: "Time",
                    width: 100
                }
            ]
        });
    }
</script>