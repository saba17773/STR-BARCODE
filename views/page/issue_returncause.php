<?php $this->layout("layouts/base", ['title' => 'Return Journal']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Return Journal</div>
  <div class="panel-body">
    <div class="btn-panel">
      <?php $detect = new \Mobile_Detect; ?>
      <?php if ($detect->isMobile()){ ?>
        <button class="btn btn-success" id="newIssue">New</button>
        <button class="btn btn-info" id="selectEmp">Select</button>
      <?php } else { ?>
        <!-- <button class="btn btn-success" id="Add">New</button> -->
        <button class="btn btn-default" id="line">Line</button>
        <?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'print_movement_issue') === true): ?>
          <button class="btn btn-info" id="print" style="display: none;">Print</button>
        <?php endif ?>
      <?php } ?>
    </div>
     <div id="grid_movement"></div>
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

<div class="modal" id="modal_add" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select Return Type</h4>
      </div>
      <div class="modal-body">
        <form id="formNewIssue">


          <div class="form-group">
            <label for="Movement_type">Return Type</label>
            <select name="Movement_type" id="Movement_type" class="form-control input-lg"  required></select>
          </div>




            <label>
              <button class="btn btn-primary" type="button"  id="returninsert" name="returninsert"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
            </label>
        </form>
      </div>
    </div>
  </div>
</div>



 <!-- alert yes or no -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
  <div class="modal-dialog modal-lm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">คุณยืนยันจะดำเนินการต่อไปหรือไม่ ?</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-btn-si">Yes</button>
        <button type="button" class="btn btn-primary" id="modal-btn-no">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {

    if (!!qs('j')) {
      $('#journalId').val(qs('j'));
      $('#requsition').val();
      $('#panel-new').addClass('hide');
      $('#panel-barcode').removeClass('hide');

      $('#grid_latest').removeClass('hide');
      grid_latest(qs('j'));

      // setInterval(function() {
      //   gojax('get', base_url+'/api/movement/issue/'+$())
      // }, 1000);

      // gojax('get', base_url + "/api/requsition_note/all")
      //   .done(function(data) {
      //     $('#requsition').html('<option value="">= กรุณาเลือก =</option>');
      //     $.each(data, function(index, el) {
      //      $('#requsition').append('<option value="'+el.ID+'">'+el.Description+'</option>');
      //     });
      //   });
    }
    else {

    }
    gojax('get', base_url + "/api/ReturnCause/Type")
      .done(function($data1) {
        $('#Movement_type').html('<option value="">= กรุณาเลือก =</option>');
        $.each($data1, function(index, mt) {
         $('#Movement_type').append('<option value="'+mt.ID+'">'+mt.Description+'</option>');
        });
      });








    $('#employee_code').on('change', function(event) {

      gojax('get', base_url+'/api/employee/'+$('#employee_code').val()+'/division')
        .done(function(data) {
          $.each(data, function(index, val) {
            open_button();
            $('#division').val(val.Description);
            $('#division_value').val(val.Code);
            //alert(val.Code);
          });
        })
        .fail(function() {
          $('#modal_alert').modal({backdrop: 'static'});
          $('#modal_alert_message').text('ไม่สามารถดึงข้อมูลได้ กรุณาลองอีกครั้ง');
          $('#top_alert').hide();
          close_button();
        });


    });



    $('#Requisition_Note').on('change', function(event) {

        $('#qty').val('1').focus();




    });


    gojax('get', base_url+'/ismobile').done(function(data) {
      if (data.status === 200) {
        grid_movement_mobile();
      } else {
        grid_movement_desktop();
      }
    });

    $('#print').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
        //alert(rowdata.JournalType);
       window.open(base_url+'/returncause_issue/print/'+rowdata.ReturnJournalID+'?mode='+rowdata.JournalType+'&create_date='+rowdata.CreatedDate, '_blank');
      }

    });

    $('#grid_movement').on('rowclick', function(event) {
      var args = event.args;
      var boundIndex = args.rowindex;
      var datarow = $("#grid_movement").jqxGrid('getrowdata', boundIndex);
      // console.log(datarow.Status);
      if (datarow.Status === 3) {
        $('#print').show();
      } else {
        $('#print').hide();
      }
    });

    $('#line').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
        $('#modal_line').modal({backdrop: 'static'});
        $('.modal-title').text(rowdata.InventJournalID);
        grid_line(rowdata.ReturnJournalID);
      }
    });







    $('#press_Batch_id').on('click', function() {
    $('#modal_Batch').modal({backdrop: 'static'});
    grid_Batch();
    });

    $('#grid_movement').on('dblclick', function() {
      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
        $('#modal_line_row').modal({backdrop: 'static'});
        $('input[name=journal_ID_show]').val(rowdata.InventJournalID);
        $('input[name=Movement_By_show]').val(rowdata.Name);
        $('input[name=journal_ID]').val(rowdata.InventJournalID);
        $('input[name=createdateIssu]').val(rowdata.CreateDate);
        if(rowdata.IdStatus == 1 || rowdata.IdStatus == 6 ||  rowdata.IdStatus == 3 )
        {
          if(rowdata.IdStatus == 1 )
          {
            $('#New_row').show();
            $('#Confirm_row').show();
            $('#Print_row').hide();
            $('#Delete_row').show();
            $('#Update_row').show();
          }
          else {
            $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').show();
            $('#Delete_row').hide();
            $('#Update_row').hide();


          }

        }
        else {
            $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').hide();
            $('#Delete_row').hide();
            $('#Update_row').hide();
        }
        // if(rowdata.IdStatus == 6)
        // {
        //   $('#Print_row').hide();
        // }
          grid_line_row(rowdata.InventJournalID);
          var requ = rowdata.JournalTypeID;


        gojax('get', base_url + '/api/movement_type/Requisitionlist/'+requ)
          .done(function(data) {
            $('#Requisition_Note').html('<option value="">= กรุณาเลือก =</option>');
            $.each(data, function(index, el) {
             $('#Requisition_Note').append('<option value="'+el.ID+'">'+el.Description+'</option>');
            });
          });
      }

    });

    $('#grid_Item').on('dblclick', function() {
      var rowdata = row_selected('#grid_Item');
      if(typeof rowdata !== 'undefined') {
        $('input[name=ItemID]').val(rowdata.ID);
        $('#modal_Item').modal('hide');
        $('input[name=press_Batch]').val('').focus();
        }
      });

      $('#grid_Batch').on('dblclick', function() {
        var rowdata = row_selected('#grid_Batch');
        if(typeof rowdata !== 'undefined') {
          $('input[name=press_Batch]').val(rowdata.Batch);
            $('#modal_Batch').modal('hide');
          }
        });

		$('#returninsert').on('click', function(event) {
			event.preventDefault();
      $('#modal_add').modal('toggle');
     var type =   $('#Movement_type') .val();

      $( "#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 600,
        modal: true,
        buttons: {
          "Yes": function() {

            gojax_f('post', base_url+'/api/journal/table/save1/'+type)
              .done(function(data) {
                if (data.status === 200) {

                      window.location = base_url+'/movement/issue/re_cause?j='+data.journal;
                        $('#grid_movement').jqxGrid('updatebounddata');
                } else {
                  $('#modal_alert').modal({backdrop: 'static'});
                   $('#modal_alert_message').text('ไม่สามารถสร้างรายการได้');
                  // $('#modal_alert_message').text(data.message);
                  $('#top_alert').hide();
                }
              });



            $( this ).dialog( "close" );
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }

      });


		});

    $('#newIssue').on('click', function(event) {
			event.preventDefault();
      $('#modal_add').modal({backdrop: 'static'});
      $("#formNewIssue")[0].reset();

		});

		$('#selectEmp').on('click', function(event) {
			event.preventDefault();

      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
        window.location = base_url+'/movement/issue/re_cause?j='+rowdata.ReturnJournalID;
      }
      else {
        $('#modal_alert').modal({backdrop: 'static'});
        $('#modal_alert_message').text('กรุณาเลืกรุณาเลือกรายการ');
        $('#top_alert').hide();
      }



		});


	});


  function grid_line(journalId) {

    var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'Barcode', type: 'string'},
          { name: 'IDItem', type: 'string' },
          { name: 'NameTH', type: 'string'},
          { name: 'Batch', type: 'string'},
          { name: 'Description', type: 'string'},
          { name: 'CreatedBy', type: 'string'},
          { name: 'CreatedDate', type: 'date'}
        ],
        url: base_url + '/api/ReturnCause/'+journalId+'/latest'
    });

    return $("#grid_line").jqxGrid({
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
      // theme : 'theme',
      columns: [
         { text:"No.", width : 50,
          cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
          }
        },
        { text: 'Barcode', datafield: 'Barcode', width: 150},
        { text: 'ItemID', datafield: 'IDItem', width: 150},
        { text: 'Name', datafield: 'NameTH', width: 200},
        { text: 'Batch', datafield: 'Batch', width: 150},
        { text: 'Return Cause', datafield: 'Description', width: 150},
        { text: 'Created By', datafield: 'CreatedBy', width: 150},
        { text: 'Create Date', datafield: 'CreatedDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150}
      ]
    });
  }

  function grid_line_row(journalId) {

    var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'ID', type: 'string'},
          { name: 'InventJournalID', type: 'string'},
          { name: 'ItemID', type: 'string' },
          { name: 'Batch', type: 'string'},
          { name: 'QTY', type: 'string'},
          { name: 'Remain', type: 'string'},
          { name: 'Issue', type: 'string'},
          { name: 'Status', type: 'string'},
          { name: 'RequsitionID', type: 'string'},
          { name: 'dateCreate', type: 'string'},
          { name: 'ITST', type: 'string'},
          { name: 'RN', type: 'string'},
          { name: 'NameTH', type: 'string'},
          { name: 'StatusName', type: 'string'},
          { name: 'TemplateSerialNo', type: 'string'}
        ],
        url: base_url + '/api/movement_issue/'+journalId+'/item'
    });
 //console.log(dataAdapter);
    return $("#grid_line_row").jqxGrid({
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
      // theme : 'theme',
      columns: [
         { text:"No.", width : 50,
          cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
          }
        },
        // { text: 'InventJournalID', datafield: 'InventJournalID', width: 150},
        { text: 'ItemID', datafield: 'ItemID', width: 100},
        { text: 'Template Serial No', datafield: 'TemplateSerialNo', width: 100},
        { text: 'Name', datafield: 'NameTH', width: 150},
        { text: 'Batch', datafield: 'Batch', width: 100},
        { text: 'Requisition Note', datafield: 'RN', width: 200},
        { text: 'QTY', datafield: 'QTY', width: 50},
        { text: 'Remain', datafield: 'Remain', width: 50},
        { text: 'Issue', datafield: 'Issue', width: 50},
        { text: 'Status', datafield: 'StatusName', width: 80}

      ]
    });
  }

  function grid_Batch() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'Batch', type: 'string'}
      ],
      url: base_url + '/api/movement_issue/Batch'
    });
    //console.log(dataAdapter);
    return $("#grid_Batch").jqxGrid({
      width: '100%',
      source: dataAdapter,
      pageable: true,
      autoHeight: true,
      filterable: true,
      showfilterrow: true,
      enableanimations: false,
      sortable: true,
      pagesize: 10,
      // theme: 'theme',
      columns: [
        //  { text:"No.", width : 50,
        //   cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
        //     return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
        //   }
        // },
        { text: 'Batch', datafield: 'Batch', width: 150}


      ]
    });
  }

  function grid_Item() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'string'},
        { name: 'NameTH', type: 'string' }

      ],
      url: base_url + '/api/movement_issue/item'
    });
    //console.log(dataAdapter);
    return $("#grid_Item").jqxGrid({
      width: '100%',
      source: dataAdapter,
      pageable: true,
      autoHeight: true,
      filterable: true,
      showfilterrow: true,
      enableanimations: false,
      sortable: true,
      pagesize: 10,
      // theme: 'theme',
      columns: [
        //  { text:"No.", width : 50,
        //   cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
        //     return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
        //   }
        // },
        { text: 'Item ID', datafield: 'ID', width: 150},
        { text: 'Name', datafield: 'NameTH', width: 400}

      ]
    });
  }

  function grid_employee() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'Code', type: 'string'},
        { name: 'FirstName', type: 'string' },
        { name: 'LastName', type: 'string'},
        { name: 'Name', type: 'string'},
        { name: 'DivisionCode', type: 'string' },
        { name: 'Department', type: 'string'}
      ],
       url: base_url + "/api/employee/all/by_status"
    });

    return $("#grid_employee").jqxGrid({
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
      // theme : 'theme',
      columns: [
        { text: 'ID', datafield: 'Code', width: 100},
	      { text: 'Name', datafield: 'Name', width: 220},
	      { text: 'Division Code', datafield: 'DivisionCode', width: 100 },
	      { text: 'Department', datafield: 'Department', width: 100}
	    ]
    });
	}

  function grid_movement_mobile() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'ReturnJournalID', type: 'string'},
            { name: 'Description', type: 'string'},
            { name: 'Name', type: 'string' },
            { name: 'CreatedDate', type: 'date' },
            { name: 'RefInventJournalID', type: 'string' },
            { name: 'namecomplete', type: 'string'},
            { name: 'CompleteDate', type: 'date'},
            { name: 'Company', type: 'string'},

          ],
          url: base_url + "/api/returncause_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          // theme : 'theme',
          columns: [
            { text: 'ReturnJournalID', datafield: 'ReturnJournalID', width: 100},
            { text: 'Journal Type', datafield: 'Description', width: 200},
            { text: 'Created By', datafield: 'Name', width: 100},
            { text: 'Create Date', datafield: 'CreatedDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150},
            { text: 'Ref.InventJournalID', datafield: 'RefInventJournalID', width: 120},
            { text: 'Complete By', datafield: 'namecomplete', width: 120},
            { text: 'Complete Date', datafield: 'CompleteDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150}
            // { text: 'Company', datafield: 'Company', width: 120}
          ]
    });
  }

	function grid_movement_desktop() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'ReturnJournalID', type: 'string'},
            { name: 'Description', type: 'string'},
            { name: 'Name', type: 'string' },
            { name: 'CreatedDate', type: 'DateTime' },
            { name: 'RefInventJournalID', type: 'string' },
            { name: 'namecomplete', type: 'string'},
            { name: 'CompleteDate', type: 'date'},
            { name: 'Company', type: 'string'},
            { name: 'Company', type: 'string'},
            { name: 'JournalType', type: 'string'},
            { name: 'Status', type: 'string'},
          ],
          url: base_url + "/api/returncause_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          // theme : 'theme',
          columns: [
            { text: 'ReturnJournalID', datafield: 'ReturnJournalID', width: 100},
            { text: 'Journal Type', datafield: 'Description', width: 200},
            { text: 'Created By', datafield: 'Name', width: 100},
            { text: 'Create Date', datafield: 'CreatedDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150},
            { text: 'Ref.InventJournalID', datafield: 'RefInventJournalID', width: 120},
            { text: 'Complete By', datafield: 'namecomplete', width: 120},
            { text: 'Complete Date', datafield: 'CompleteDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150}
            // { text: 'Company', datafield: 'Company', width: 120}

          ]
    });
	}




</script>
