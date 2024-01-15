<?php $this->layout("layouts/handheldmobile", ['title' => 'Withdrawal No.']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;" id="fromserchWithdrawal">
    <div class="panel-heading">Withdrawal No.</div>
    <div class="panel-body">
        <form id="form_tracking">
            <div class="form-group">
                <input type="text" class="form-control input-lg" name="search" id="search" placeholder="Withdrawal code." required>
            </div>
        </form>

    </div>
</div>

<!-- Modal -->


<div id="data_row_line" style="display: none;">
    <div style="margin-bottom: 20px;">
        <table class="table" style="background: #ffffff;">
            <tr>
                <td width="20%">

                    journal ID: <input type="text" name="journal_ID_show" id="journal_ID_show" class="form-control" readonly>
                    <input type="hidden" name="checkdbck" id="checkdbck" class="form-control" value="" readonly>


                </td>
                <td width="20%">

                </td>
                <td>

                </td>
            </tr>
        </table>
        <table class="table">
            <tr>
                <td width="30%">
                    <div class="btn-panel">
                        <button class="btn btn-lg" id="Movement_button" style="background: green; color: #ffffff; "> <span class="glyphicon glyphicon-download-alt"></span>Movement</button><span style="padding-right: 20px;"></span>

                        <button class="btn btn-lg" id="Return_button" style="background: orange; color: #ffffff; "> <span class="glyphicon glyphicon-log-out"></span> Return</button><span style="padding-right: 20px;"></span>

                    </div>
                    <!-- <button class="btn btn-success btn-lg pull-left" id="Movement_button" style="margin: 10px 0px;">
                   Movement</button>

                  <button class="btn btn-warning btn-lg pull-left" id="Return_button" style="margin: 10px 0px;">
                     Return
                  </button> -->
                </td>

                <td align="center">
                    <form id="chekbarcode">
                        <input type="text" id="serchbarcode" name="serchbarcode" placeholder="Movement" class="form-control input-lg" autocomplete="off">
                        <input type="hidden" id="item" name="item" placeholder="item" class="form-control input-lg">
                        <input type="hidden" id="JournalType" name="JournalType" placeholder="item" class="form-control input-lg">
                        <input type="hidden" name="journal_ID_type" id="journal_ID_type" class="form-control" readonly>
                        <input type="hidden" name="RequsitionID_code" id="RequsitionID_code" class="form-control" readonly>
                        <input type="hidden" name="CheckTypeserch" id="CheckTypeserch" class="form-control" readonly>
                        <input type="hidden" id="TemplateSerialNo" name="TemplateSerialNo" placeholder="TemplateSerialNo" class="form-control input-lg">
                    </form>

                </td>
                <td width="30%">
                    <button class="btn btn-primary btn-lg pull-right" id="back_button" style="margin: 10px 0px;">
                        <span class="glyphicon glyphicon-arrow-left"></span> ย้อนกลับ
                    </button>
                </td>
            </tr>
        </table>
    </div>

    <div id="grid_line_row"></div>
</div>

<!--Modal select item to pick-->
<div class="modal" id="modal_select_row_pick" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Select Item</h4>
            </div>
            <div class="modal-body">
                <!-- Content -->
                <div id="list_item_line" class="list-group" style="font-weight: bold; font-size: 2em;"></div>
            </div>
        </div>
    </div>
</div>





<script>
    jQuery(document).ready(function($) {

        var WithdrawalID = $("input[name=search]");
        WithdrawalID.val('').focus();

        // $('#modal_detail').on('hidden.bs.modal', function() {
        // 	$(onFocus).val('').focus();
        // });

        $('#form_tracking').submit(function(e) {
            e.preventDefault();
            if ($.trim(WithdrawalID.val()) !== '') {
                gojax_f('post', base_url + '/api/search/Withdrawal', '#form_tracking')
                    .done(function(data) {
                        if (data.status == 200) {
                            $('input[name=journal_ID_show]').val(data.message);
                            $('input[name=journal_ID_type]').val(data.message);
                            $('input[name=checkdbck]').val(0);
                            $('#fromserchWithdrawal').hide();
                            $('#data_row_line').show();
                            grid_line_row(journalId = data.message);
                            $('#Return_button').show();
                            $('#Movement_button').show();
                            $('#back_button').hide();
                            $('#serchbarcode').hide();
                            onFocus = 'input[name=search]';
                        } else {

                            alert(data.message);
                            // $('#modal_alert').modal({
                            //     backdrop: 'static',
                            //     keyboard: false
                            // });
                            // $('#modal_alert_message').text(data.message);

                            // $('#modal_alert').on('click', function() {
                            WithdrawalID.val('').focus();
                            //});

                            //WithdrawalID.val('').focus();
                        }
                    });
            } else {
                WithdrawalID.val('').focus();
            }
            WithdrawalID.val('').focus();
        });

        $('#chekbarcode').submit(function(e) {
            e.preventDefault();
            var barcode = $("input[name=serchbarcode]");
            var ItemID = $("input[name=item]");
            if ($.trim(barcode.val()) !== '') {
                if ($.trim(ItemID.val()) == '') {
                    $('#modal_alert').modal({
                        backdrop: 'static'
                    });
                    $('#modal_alert_message').text('กรุณาเลือก Item');
                    $('#grid_line_row').jqxGrid('updatebounddata');

                    $('#modal_alert').on('click', function() {
                        barcode.val('').focus();
                    });

                } else {
                    gojax_f('post', base_url + '/api/Withdrawal/save', '#chekbarcode')
                        .done(function(data) {
                            if (data.status == 200) {
                                $('#top_alert').show();
                                $('#top_alert_message').text(data.CuringCode + ', ' + data.Batch + ' => ' + data.barcode);
                                $('#modal_alert').modal('hide');
                                //	$('#modal_alert_pass').modal({backdrop: 'static'});
                                //$('#modal_alert_message_pass').text(data.message);
                                //$('#grid_line_row').jqxGrid('updatebounddata');
                                barcode.val('').focus();
                                //	ItemID.val('')
                            } else {
                                $('#top_alert').hide();
                                $('#modal_alert').modal({
                                    backdrop: 'static'
                                });
                                $('#modal_alert_message').text(data.message);
                                // $('#top_alert_message').hide()
                                // $('#grid_line_row').jqxGrid('updatebounddata');
                                //barcode.val('').focus();
                                //ItemID.val('')

                            }
                            $('#grid_line_row').jqxGrid('updatebounddata');
                            $('#modal_alert').on('click', function() {
                                barcode.val('').focus();
                            });

                        });

                }
            } else {
                barcode.val('').focus();
            }
            $('#grid_line_row').jqxGrid('clearselection');

        });

        $('#back_button').on('click', function() {
            var WithdrawalID = $("input[name=serchbarcode]");
            var ItemID = $("input[name=item]");
            $('input[name=checkdbck]').val(0);
            $('input[name=CheckTypeserch]').val('');
            ItemID.val('')
            $('#fromserchWithdrawal').hide();
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('#Return_button').show();
            $('#Movement_button').show();
            $('#back_button').hide();
            $('#serchbarcode').hide();
            $('#top_alert').hide();
            WithdrawalID.val('').focus();
        });

        $('#Movement_button').on('click', function() {
            var WithdrawalID = $("input[name=serchbarcode]");
            var ItemID = $("input[name=item]");
            $('#serchbarcode').attr("placeholder", "Movement");
            $('input[name=checkdbck]').val(1);
            $('input[name=CheckTypeserch]').val(0);
            $('#Return_button').hide();
            $('#Movement_button').hide();
            $('#back_button').show();
            $('#serchbarcode').show();
            $('#fromserchWithdrawal').hide();
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('#grid_line_row').jqxGrid('clearselection');
            WithdrawalID.val('').focus();
            ItemID.val('');
        });

        $('#Return_button').on('click', function() {
            var ItemID = $("input[name=item]");
            var WithdrawalID = $("input[name=serchbarcode]");
            $('input[name=checkdbck]').val(1);
            $('input[name=CheckTypeserch]').val(1);
            $('#serchbarcode').attr("placeholder", "Return");
            $('#Return_button').hide();
            $('#Movement_button').hide();
            $('#back_button').show();
            $('#serchbarcode').show();
            $('#fromserchWithdrawal').hide();
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('#grid_line_row').jqxGrid('clearselection');
            WithdrawalID.val('').focus();
            ItemID.val('');
        });

        $('#grid_line_row').on('rowselect', function() {

            var rowdata = $('#grid_line_row').jqxGrid('getrows');
            var checkissu = $('input[name=CheckTypeserch]').val();
            var checkdbck = $('input[name=checkdbck]').val();
            if (checkdbck == 1) {
                $('#modal_select_row_pick').modal({
                    backdrop: 'static'
                });
            }

            $('#list_item_line').html('');

            // if($('input[name=checkdbck]').val()==1) TemplateSerialNo
            // {

            // if(checkissu === 0)
            // {
            $.each(rowdata, function(index, el) {
                if (checkissu === '0') {
                    $('#list_item_line').append('<a href="#" onClick="return setRowSelect(' + el.ID + ',\'' + el.JournalTypeID + '\',' + el.RequsitionID + ',\'' + el.TemplateSerialNo + '\'' + ')" class="list-group-item">' + el.ItemID + ' ' + el.Batch + ' (จำนวน' + el.Remain + ')' + ' ' + el.TemplateSerialNo + '<BR>' + el.RN + '</a>');
                    //$('#list_item_line').append('<a href="#" onClick="return setRowSelect('+el.ID+',\''+el.JournalTypeID+'\','+el.RequsitionID+')" class="list-group-item">'+el.ItemID+' '+el.Batch+' (จำนวน'+el.Remain+')</a>');
                    //	alert(el.ID)
                } else if (checkissu === '1') {

                    $('#list_item_line').append('<a href="#" onclick="return setRowSelect(' + el.ID + ',\'' + el.JournalTypeID + '\',' + el.RequsitionID + ',\'' + el.TemplateSerialNo + '\'' + ')" class="list-group-item">' + el.ItemID + ' ' + el.Batch + ' (จำนวน' + el.Issue + ')' + ' ' + el.TemplateSerialNo + '<BR>' + el.RN + '</a>');
                    //	$('#list_item_line').append('<a href="#" onclick="return setRowSelect('+el.ID+',\''+el.JournalTypeID+'\','+el.RequsitionID+')" class="list-group-item">'+el.ItemID+' '+el.Batch+' (จำนวน'+el.Issue+')</a>');
                }
                // }



            });

            //}
            // else
            // {
            //   $.each(rowdata, function(index,el) {
            // 		$('#list_item_line').append('<a href="#" onclick="return setRowSelect('+el.ID+',\''+el.JournalTypeID+'\','+el.RequsitionID+')" class="list-group-item">'+el.ItemID+' '+el.Batch+' (จำนวน'+el.Issue+')'+'<BR>'+el.RN+'</a>');});
            // }
            //	 $('#grid_line_row').selection.Clear();
            //$('#grid_line_row').jqxGrid('clearselection');
            //	}

        });







    });

    function grid_line_row(journalId) {

        var dataAdapter = new $.jqx.dataAdapter({
            datatype: 'json',
            datafields: [{
                    name: 'ID',
                    type: 'string'
                },
                {
                    name: 'InventJournalID',
                    type: 'string'
                },
                {
                    name: 'ItemID',
                    type: 'string'
                },
                {
                    name: 'Batch',
                    type: 'string'
                },
                {
                    name: 'QTY',
                    type: 'string'
                },
                {
                    name: 'Remain',
                    type: 'string'
                },
                {
                    name: 'Issue',
                    type: 'string'
                },
                {
                    name: 'Status',
                    type: 'string'
                },
                {
                    name: 'RequsitionID',
                    type: 'string'
                },
                {
                    name: 'dateCreate',
                    type: 'string'
                },
                {
                    name: 'ITST',
                    type: 'string'
                },
                {
                    name: 'NameTH',
                    type: 'string'
                },
                {
                    name: 'RN',
                    type: 'string'
                },
                {
                    name: 'JournalTypeID',
                    type: 'string'
                },
                {
                    name: 'StatusName',
                    type: 'string'
                },
                {
                    name: 'TemplateSerialNo',
                    type: 'string'
                }
            ],
            url: base_url + '/api/movement_issue/' + journalId + '/item'
        });
        //console.log(dataAdapter);
        return $("#grid_line_row").jqxGrid({
            width: '100%',
            source: dataAdapter,
            autoheight: true,
            // pageSize : 10,
            rowsheight: 50,
            // columnsheight : 40,
            altrows: true,
            // pageable : true,
            // sortable: true,
            filterable: true,
            showfilterrow: true,
            columnsresize: true,
            theme: 'theme',
            columns: [{
                    text: "No.",
                    width: 50,
                    cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
                        return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
                    }
                },

                {
                    text: 'ItemID',
                    datafield: 'ItemID',
                    width: 100
                },
                {
                    text: 'Template Serial No',
                    datafield: 'TemplateSerialNo',
                    width: 100
                },
                {
                    text: 'Name',
                    datafield: 'NameTH',
                    width: 400
                },
                {
                    text: 'Batch',
                    datafield: 'Batch',
                    width: 80
                },
                {
                    text: 'Requisition Note',
                    datafield: 'RN',
                    width: 300
                },
                {
                    text: 'QTY',
                    datafield: 'QTY',
                    width: 50
                },
                {
                    text: 'Remain',
                    datafield: 'Remain',
                    width: 50
                },
                {
                    text: 'Issue',
                    datafield: 'Issue',
                    width: 50
                },
                {
                    text: 'Status',
                    datafield: 'StatusName',
                    width: 100,
                    filtertype: 'checkedlist',
                    cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
                        var cl;

                        if (rowdata.StatusName == 'Open') {
                            cl = 'white';
                        } else if (rowdata.StatusName == 'In-progress') {
                            cl = 'orange';
                        } else if (rowdata.StatusName == 'Confirm') {
                            cl = 'blue';
                        } else if (rowdata.StatusName == 'Completed') {
                            cl = 'green';
                        }
                        return '<div style=\'padding: 15px; height: 60px; background : ' + cl + ' ; color:#000000;\'> ' + value + ' </div>';
                    }
                }


            ]
        });
    }

    function setRowSelect(id, JournalTypeID, RequsitionID_code, TemplateSerialNo) {
        //alert(TemplateSerialNo);

        $('input[name=item]').val(id);
        $('input[name=JournalType]').val(JournalTypeID);
        $('input[name=RequsitionID_code]').val(RequsitionID_code);
        $('input[name=TemplateSerialNo]').val(TemplateSerialNo);
        var WithdrawalID = $("input[name=serchbarcode]");
        var check = $('input[name=item]').val();
        //	alert($('input[name=item]').val()+"--"+id);

        // if(check !== id)
        // {
        //	$('#grid_line_row').jqxGrid('selectrow', id);
        $('#modal_select_row_pick').modal('hide');
        // }

        //alert(id);



        WithdrawalID.val('').focus();
        //alert(123);

    }
</script>