<?php $this->layout("layouts/base", ['title' => 'BOI Master.']); ?>

<div class="head-space"></div>

<div class="panel panel-default">
    <div class="panel-heading">Round Master.</div>
    <div class="panel-body">
        <div class="btn-panel">
            <button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
            <button class="btn btn-info" id="edit">Edit</button>
            <button class="btn btn-danger" id="delete">Delete</button>
            <button id="print" class="btn btn-default">Print</button>
        </div>

        <div id="grid_round"></div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Create new</h4>
            </div>
            <div class="modal-body">
                <form id="form_create" onsubmit="return submit_create()">

                    <input type="hidden" name="building_id" id="building_id" class="form-control" autocomplete="off" required>
                    <input type="hidden" name="delete_id" id="delete_id" class="form-control" autocomplete="off" required>


                    <div class="form-group">
                        <label for="ID">ID</label>
                        <input type="text" name="round_id" id="round_id" class="form-control" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="round_desc">Description</label>
                        <input type="text" name="round_desc" id="round_desc" class="form-control" autocomplete="off" required>
                    </div>

                    <!-- <table>
                        <tr>
                            <td><b>ID :</b><input type="text" id="idround" name="idround">
                            <td>
                        </tr>
                        <tr>
                            <td><b>Description :</b><input type="text" name="round_desc" id="round_desc" class="form-control" autocomplete="off" required>
                            <td>
                        </tr>
                    </table> -->

                    <input type="hidden" name="form_type">
                    <input type="hidden" name="_id">
                    <button class="btn btn-primary" type="submit">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function($) {

        grid_round();

        $('#edit').on('click', function(e) {
            var rowdata = row_selected("#grid_round");
            if (typeof rowdata !== 'undefined') {
                $('#modal_create').modal({
                    backdrop: 'static'
                });
                $('input[name=form_type]').val('update');
                // $('input[name=building_id]').prop('readonly', true);
                $('.modal-title').text('Update');
                // $('input[name=building_id]').val(rowdata.ID);
                $('input[name=round_id]').val(rowdata.Id).prop('readonly', true);
                $('input[name=round_desc]').val(rowdata.Description);


            }

        });

        $('#delete').on('click', function(event) {
            event.preventDefault();
            var rowdata = row_selected('#grid_round');
            $('input[name=form_type]').val('delete');
            $('input[name=delete_id]').val(rowdata.Id);
            //alert(rowdata.ID); exit();
            if (!!rowdata) {
                if (confirm('Are you sure?')) {
                    gojax_f('post', base_url + '/api/round/create', '#form_create')
                        .done(function(data) {
                            if (data.status != 200) {
                                //	gotify(data.message, 'danger');
                                alert(data.message);
                            } else {
                                alert(data.message);
                                $('#grid_round').jqxGrid('updatebounddata');
                            }
                        });

                }
            }
        });

        $('#print').on('click', function() {
            var rowdata = row_selected('#grid_round');
            if (typeof rowdata !== 'undefined') {
                window.open(base_url + '/generator/round/a5/' + rowdata.Id, '_blank');
            }

        });



    });

    function modal_create_open() {
        $('#form_create').trigger('reset');
        $('input[name=form_type]').val('create');
        $('.modal-title').text('Create new');
        $('input[name=round_id]').prop('readonly', true);

    }

    function submit_create() {
        // var building_id = $('input[name=round_id]').val();
        var round_desc = $('input[name=round_desc]').val();
        //alert(round_desc);

        if (round_desc) {
            gojax_f('post', base_url + '/api/round/create', '#form_create')
                .done(function(data) {
                    if (data.status != 200) {
                        //	gotify(data.message, 'danger');
                        alert(data.message);
                    } else {
                        //	alert(data.message);
                        $('#modal_create').modal('hide');
                        $('#grid_round').jqxGrid('updatebounddata');
                    }
                });
        }
        return false;
    }

    function grid_round() {
        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [{
                    name: 'Id',
                    type: 'string'
                },
                {
                    name: 'Description',
                    type: 'string'
                },
                {
                    name: 'CreateDate',
                    type: 'string'
                }

            ],
            url: base_url + "/api/press/allround"
        });

        return $("#grid_round").jqxGrid({
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
            // theme : 'theme',
            columns: [{
                    text: 'check',
                    datafield: 'ID',
                    width: 150,
                    columntype: 'checkbox',
                    filtertype: 'bool'
                },
                {
                    text: 'ID',
                    datafield: 'Id',
                    width: 150
                },
                {
                    text: 'Description',
                    datafield: 'Description',
                    width: 150
                }


            ]
        });
    }
</script>