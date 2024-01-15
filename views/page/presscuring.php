<?php $this->layout("layouts/base", ['title' => 'Press']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style=" max-width: 500px; margin: auto;display: none " id="form-curing">
    <div class="panel-heading">Select CuringCode</div>
    <div class="panel-body">
        <div class="btn-panel">
            <form id="form_CreateCuring" onsubmit="return submit_createcuring()">


                <div class=" btn-group btn-group-justified" role="group">
                    <div class="btn-group">
                        <label for="CuringL">CuringCode L</label><br>
                        <select name="selectMenuCuringL[]" id="selectMenuCuringL" multiple="multiple" style="width: 200px">
                        </select>
                    </div>
                    <div class="btn-group">
                        <label for="CuringL">CuringCode R</label><br>
                        <select name="selectMenuCuringR[]" id="selectMenuCuringR" multiple="multiple" style="width: 200px">
                        </select>
                    </div>
                </div>

                <input type="hidden" value="" name="IdPress" id="IdPress" />

                <BR>
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>


    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Press</div>
    <div class="panel-body">
        <!-- <div class="btn-panel">
            <button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_create">Create</button>
            <button class="btn btn-info" id="edit">Edit</button>
            <button class="btn btn-danger" id="delete">Delete</button>
        </div> -->

        <div id="grid_press"></div>
    </div>
</div>





<script>
    jQuery(document).ready(function($) {

        grid_press();

        $('#selectMenuCuringL').html("");
        $('#selectMenuCuringL').multipleSelect({
            single: true,
            filter: true
        });

        $('#selectMenuCuringR').html("");
        $('#selectMenuCuringR').multipleSelect({
            single: true,
            filter: true
        });

        getPressSideGT()
            .done(function(data) {
                // $('#selectMenuCuringL').append('<option value=""></option>');
                $.each(data, function(k, v) {
                    $('#selectMenuCuringL').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                    $('#selectMenuCuringR').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                });
                $('#selectMenuCuringL').multipleSelect({
                    single: true
                });
                $('#selectMenuCuringR').multipleSelect({
                    single: true
                });
            });


        $('#grid_press').on('dblclick', function() {



            var rowdata = row_selected('#grid_press');
            if (typeof rowdata !== 'undefined') {
                // alert(rowdata.CuringCodeL);

                $('input[name=IdPress]').val(rowdata.ID);



                if (rowdata.CuringCodeL == null) {

                    $('#selectMenuCuringL').html("");
                    $('#selectMenuCuringR').html("");

                    getPressSideGT()
                        .done(function(data) {
                            // $('#selectMenuCuringL').append('<option value=""></option>');
                            $.each(data, function(k, v) {
                                $('#selectMenuCuringL').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                                $('#selectMenuCuringR').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                            });


                            $('#selectMenuCuringL').multipleSelect({
                                single: true
                            });
                            $('#selectMenuCuringR').multipleSelect({
                                single: true
                            });
                        });

                } else {

                    $('#selectMenuCuringL').html("");
                    $('#selectMenuCuringR').html("");

                    getPressSideGT()
                        .done(function(data) {
                            // $('#selectMenuCuringL').append('<option value=""></option>');
                            $.each(data, function(k, v) {
                                $('#selectMenuCuringL').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                                $('#selectMenuCuringR').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                            });

                            $('#selectMenuCuringL').val(rowdata.CuringCodeL);
                            $('#selectMenuCuringR').val(rowdata.CuringCodeR);

                            $('#selectMenuCuringL').multipleSelect({
                                single: true
                            });
                            $('#selectMenuCuringR').multipleSelect({
                                single: true
                            });
                        });

                }


                document.getElementById("form-curing").style.display = "";




            }

        });
    });



    function submit_createcuring() {

        if ($('#selectMenuCuringL').val() == null || $('#selectMenuCuringR').val() == null) {
            alert("กรุณาใส่ข้อมูลให้ครบ");
            return false;
        }
        // alert($('#selectMenuCuringL').val());
        // alert($('#selectMenuCuringR').val());
        // if (!!id && !!desc && !!building_BOI) {
        gojax_f('post', base_url + '/api/press/createcuring', '#form_CreateCuring')
            .done(function(data) {
                if (data.status == 404) {
                    gotify(data.message, 'danger');
                } else {
                    // $('#modal_create').modal('hide');
                    document.getElementById("form-curing").style.display = "none";
                    $('#grid_press').jqxGrid('updatebounddata');
                    // alert(data.message);
                }
            });
        //  }
        // return false;
    }

    function grid_press() {
        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [{
                    name: 'ID',
                    type: 'string'
                },
                {
                    name: 'Description',
                    type: 'string'
                },
                {
                    name: 'BOI',
                    type: 'string'
                },
                {
                    name: 'BOIName',
                    type: 'string'
                },
                {
                    name: 'CuringCodeL',
                    type: 'string'
                },
                {
                    name: 'CuringCodeR',
                    type: 'string'
                }
            ],
            url: base_url + "/api/press/all"
        });

        return $("#grid_press").jqxGrid({
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
                    text: 'ID',
                    datafield: 'ID',
                    width: 100
                },
                {
                    text: 'Description',
                    datafield: 'Description',
                    width: 100
                },
                {
                    text: 'BOI',
                    datafield: 'BOIName',
                    width: 150
                },
                {
                    text: 'CuringCodeL',
                    datafield: 'CuringCodeL',
                    width: 150
                },
                {
                    text: 'CuringCodeR',
                    datafield: 'CuringCodeR',
                    width: 150
                }
            ]
        });
    }



    function getPressSideGT() {
        return $.ajax({
            url: base_url + '/api/curecode/curecodemaster',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }
</script>