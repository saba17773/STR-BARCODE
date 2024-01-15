<?php $this->layout("layouts/base", ['title' => 'Movement']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<!-- <#?php  
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";
?> -->
<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Movement Issue</div>
  <div class="panel-body">
    <div class="btn-panel">
      <?php $detect = new \Mobile_Detect; ?>
      <?php if ($detect->isMobile()) { ?>
        <button class="btn btn-success" id="newIssue">New</button>
        <button class="btn btn-info" id="selectEmp">Select</button>
      <?php } else { ?>
        <div style="display: flex; justify-content: start; align-items: center; gap: 10px;">
          <button class="btn btn-success" id="Add">New</button>
          <button class="btn btn-default" id="line">Line</button>
          <button class="btn btn-default" id="postback">Post back</button>
          <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'print_movement_issue') === true) : ?>
            <!-- <button class="btn btn-info" id="print" style="display: none;">Print</button> -->
            <div class="dropdown">
              <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Print
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="#" id="print">รายงานเบิกยาง Detail</a></li>
                <li><a href="#" id="print_summary">รายงานเบิกยาง Summary</a></li>

              </ul>
            </div>
        </div>
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
        <button class="btn btn-success" id="export">Export</button>
        <br>
        <br>
        <!-- Content -->
        <div id="grid_line"></div>
        <input type="hidden" id="JornalId" name="JornalId" />
      </div>
    </div>
  </div>
</div>

<!-- Line Modal -->
<!-- <div class="modal" id="modal_add" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <span class="glyphicon glyphicon-remove"></span>
        </button>
        <h4 class="modal-title">Create new Movement Issue</h4>
      </div>
      <div class="modal-body">

        <div class="row">




            <div class="panel panel-defeult" id="panel-new">
              <div class="panel-body">
                <form id="formNewIssue">
                  <div class="form-group">
                    <label for="employee_code">Employee Code</label>
                    <select name="employee_code" id="employee_code" class="form-control input-lg"  required></select>
                  </div>
                  <div class="form-group">
                    <label for="division">Department</label>
                    <input type="text" name="division" id="division" class="form-control input-lg" readonly required>
                    <input type="hidden" name="division_value" id="division_value" class="form-control input-lg" readonly required>
                  </div>
                  <div class="form-group">
                    <label for="Movement_type">Movement type</label>
                    <select name="Movement_type" id="Movement_type" class="form-control input-lg"  required></select>
                  </div>
                  <div class="form-group">
                    <label for="usercheck">USER:</label>
                    <input type="text" name="user" id="user" class="form-control input-lg" >
                  </div>
                  <div class="form-group">
                    <label for="passwordcheck">Password:</label>
                    <input type="password" name="pass" id="pass" class="form-control input-lg" required>
                  </div>


                      <button class="btn btn-success" type="submit">Save</button>
                </form>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div> -->

<div class="modal" id="modal_add" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new Movement Issue</h4>
      </div>
      <div class="modal-body">
        <form id="formNewIssue">
          <div class="form-group">
            <label for="employee_code">Employee Code</label>
            <select name="employee_code" id="employee_code" class="form-control input-lg" required></select>
          </div>
          <div class="form-group">
            <label for="division">Department</label>
            <input type="text" name="division" id="division" class="form-control input-lg" readonly required>
            <input type="hidden" name="division_value" id="division_value" class="form-control input-lg" readonly required>
          </div>
          <div class="form-group">
            <label for="Movement_type">Movement type</label>
            <select name="Movement_type" id="Movement_type" class="form-control input-lg" required></select>
          </div>
          <div class="form-group">
            <label for="usercheck">USER:</label>
            <input type="text" name="user" id="user" class="form-control input-lg">
          </div>
          <div class="form-group">
            <label for="passwordcheck">Password:</label>
            <input type="password" name="pass" id="pass" class="form-control input-lg" required>
          </div>

          <!-- <button class="btn btn-primary btn-lg btn-block" type="submit" hidden ="hidden">Save</button> -->
          <!-- <button  type="submit" hidden ="hidden"></button> -->
          <label>
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
          </label>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Line Modal -->
<div class="modal" id="modal_line_row" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="row">
          <div class="col-xs-2">
            journal ID: <input type="text" id="journal_ID_show" name="journal_ID_show" class="form-control" readonly>
          </div>
          <div class="col-xs-5">
            Movement By : <input type="text" id="Movement_By_show" name="Movement_By_show" class="form-control" readonly>
          </div>

        </div>
        <!-- journal ID :<input type="text" value="" name="journal_ID_show" id="journal_ID_show">
        Movement By :<input type="text" value="" name="Movement_By_show" id="Movement_By_show"> -->

        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">


        <button class="btn btn-warning" id="Print_row">Print</button>
        <button class="btn btn-success" id="New_row">New</button>
        <button class="btn btn-primary" id="Update_row">Update</button>
        <button class="btn btn-danger" id="Delete_row">Delete</button>
        <button class="btn btn-info" id="Confirm_row">Confirm</button>
        <input type="hidden" id="checkauth" name="checkauth" value="">
        <input type="hidden" id="checkstatusL" name="checkstatusL" value="">
        <BR><BR>
        <div id="grid_line_row"></div>

      </div>
    </div>
  </div>
</div>

<!-- Line New -->
<!-- <div class="modal" id="modal_New" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        	<span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><div id="toppic"></div></h4>
      </div>
      <div class="panel panel-default form-center">
	<div class="panel-heading"></div>
  <div class="panel-body">
	<form id="formNewIssue_row">
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="press_no">Item ID.</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control" name="ItemID" id="ItemID" required readonly>
				      <span class="input-group-btn">
				        <button class="btn btn-info" id="Press_Item" type="button">
				        	<span class="glyphicon glyphicon-search"></span> เลือก Item</span>
				        </button>

				      </span>
              <input type="hidden" value="" name="journal_ID" id="journal_ID">
              <input type="hidden" value="" name="checkstatus" id="checkstatus">
              <input type="hidden" value="" name="ID_row" id="ID_row">
              <input type="hidden" value="" name="createdateIssu" id="createdateIssu">
				    </div>

				</div>
			</div>
		</div>

    <div class="form-group">
			<div class="row">
				<div class="col-md-12">
					<label for="press_no">Batch</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="input-group">
				      <input type="text" class="form-control" name="press_Batch" id="press_Batch" required readonly>
				      <span class="input-group-btn">
				        <button class="btn btn-info" id="press_Batch_id" type="button">
				        	<span class="glyphicon glyphicon-search"></span> เลือก Batch
				        </button>
				      </span>
				    </div>

				</div>
			</div>
		</div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-12">
          <label for="press_no">Requisition Note</label>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <select name="Requisition_Note" id="Requisition_Note" class="form-control"  required></select>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="row">
        <div class="col-md-12">
          <label for="qty">QTY</label>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <input name="qty" id="qty" class="form-control" type="number"  min="1" value="" required>
        </div>
      </div>
    </div>
    <div class="btn-panel">


        	<button class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved" type="submit"></span> Save</button>

    </div>
	</form>
  </div>
</div>

    </div>
  </div>
</div> -->

<div class="modal" id="modal_New" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
          <div id="toppic"></div>
        </h4>
      </div>
      <div class="modal-body">
        <form id="formNewIssue_row">
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label for="press_no">Template Serial No.</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" class="form-control" name="TemplateSN" id="TemplateSN">
                  <span class="input-group-btn">
                    <button class="btn btn-info" id="checkSN" type="button">
                      <span class="glyphicon glyphicon-search"></span> Check S/N
                    </button>
                  </span>
                </div>

              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label for="press_no">Item ID.</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" class="form-control" name="ItemID" id="ItemID" required onkeypress="return false;" autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-info" id="Press_Item" type="button">
                      <span class="glyphicon glyphicon-search"></span> เลือก Item</span>
                  </button>

                  </span>
                  <input type="hidden" value="" name="journal_ID" id="journal_ID">
                  <input type="hidden" value="" name="checkstatus" id="checkstatus">
                  <input type="hidden" value="" name="ID_row" id="ID_row">
                  <input type="hidden" value="" name="createdateIssu" id="createdateIssu">
                  <input type="hidden" value="" name="_qtycheck" id="_qtycheck">
                  <input type="hidden" value="0" name="checkSNbutton" id="checkSNbutton">
                </div>

              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label for="press_no">Batch</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" class="form-control" name="press_Batch" id="press_Batch" required="required" onkeypress="return false;" autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-info" id="press_Batch_id" type="button">
                      <span class="glyphicon glyphicon-search"></span> เลือก Batch
                    </button>
                  </span>
                </div>

              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label for="press_no">Requisition Note</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <select name="Requisition_Note" id="Requisition_Note" class="form-control" required></select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label for="qty">QTY</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <input name="qty" id="qty" name="qty" class="form-control" type="number" min="1" value="" required autocomplete="off">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <input name="status" id="status" name="status" class="form-control" type="hidden" min="1" value="" required autocomplete="off">
            </div>
          </div>
          <div class="btn-panel">

            <!-- <button class="btn btn-success" type="submit">Save</button>
              <button class="btn btn-warning" type="reset">Cancel</button> -->
            <button class="btn btn-primary" id="submitNewRow"><span class="glyphicon glyphicon-floppy-saved" type="submit"></span> Save</button>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ITEM Modal -->
<!-- <div class="modal" id="modal_Item" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div id="grid_Item"></div>
      </div>
    </div>
  </div>
</div> -->

<div class="modal" id="modal_Item" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Item</h4>
      </div>
      <div class="modal-body">
        <form id="form_create_item">
          <div class="form-group">
            <div id="grid_Item"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>

<!-- ITEM Batch -->
<!-- <div class="modal" id="modal_Batch" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div id="grid_Batch"></div>
      </div>
    </div>
  </div>
</div> -->

<div class="modal" id="modal_Batch" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Batch</h4>
      </div>
      <div class="modal-body">
        <form id="form_create_batch">
          <div class="form-group">
            <div id="grid_Batch"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">

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


<!-- modal postback invent journal -->
<div class="modal" id="modal_postback" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title-postback"></h4>
      </div>
      <div class="modal-body">
        <form id="formPostBackJournal">
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label>Username :</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <input name="userPostBack" id="userPostBack" class="form-control" required autocomplete="off">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-12">
                <label>Password :</label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <input type="password" class="form-control" name="passPostBack" id="passPostBack" autocomplete="off" required>
              </div>
            </div>
          </div>
          <div class="btn-panel">
            <button class="btn btn-success" id="btnConfirmPostBack">Confirm</button>
          </div>
          <input type="hidden" name="txtJournalID" id="txtJournalID" class="form-control" readonly required autocomplete="off">
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  jQuery(document).ready(function($) {

    $('li > a#print_summary ').hide();
    $('input[name=qty]').on('keyup', function(e) {
      var actualQty = Number($('input[name=_qtycheck]').val());
      var currentQty = Number($('input[name=qty]').val());
      // alert(actualQty);
      // alert(currentQty);
      if (currentQty > actualQty ||
        currentQty === 0 ||
        typeof currentQty === 'undefined' ||
        currentQty === "" || $(this).val() == "") {
        // alert(actualQty);
        // alert(currentQty);
        $('button#submitNewRow').prop('disabled', false);

      } else {
        if ($(this).val() <= 0) {
          alert(" จำนวนเต็มที่มากกว่า 0");
          $('input[name=qty]').val('').focus();
          $('button#submitNewRow').prop('disabled', true);
        }
        $('button#submitNewRow').prop('disabled', false);

      }
    });







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

      gojax('get', base_url + "/api/requsition_note/all")
        .done(function(data) {
          $('#requsition').html('<option value="">= กรุณาเลือก =</option>');
          $.each(data, function(index, el) {
            $('#requsition').append('<option value="' + el.ID + '">' + el.Description + '</option>');
          });
        });
    } else {
      gojax('get', base_url + "/api/employee/all/by_status")
        .done(function(data) {
          $('#employee_code').html('<option value="">= กรุณาเลือก =</option>');
          $.each(data, function(index, el) {
            $('#employee_code').append('<option value="' + el.Code + '">' + el.Name + '</option>');
            // console.log(el);
          });
        });

      gojax('get', base_url + "/api/movement_type/all")
        .done(function($data1) {
          $('#Movement_type').html('<option value="">= กรุณาเลือก =</option>');
          $.each($data1, function(index, mt) {
            $('#Movement_type').append('<option value="' + mt.ID + '">' + mt.Description + '</option>');
          });
        });



    }

    $('#formNewIssue').on('submit', function(event) {
      $('#modal_add').modal('hide');



      event.preventDefault();
      // $('#employee_code').css({'background': '#eeeeee'}).prop('readonly', true);
      // $('#user').css({'background': '#eeeeee'}).prop('readonly', true);
      // $('#pass').css({'background': '#eeeeee'}).prop('readonly', true);
      $("#dialog-confirm").dialog({
        resizable: false,
        height: "auto",
        width: 600,
        modal: true,
        buttons: {
          "Yes": function() {

            gojax_f('post', base_url + '/api/journal/table/saveDestop', '#formNewIssue')
              .done(function(data) {
                if (data.status === 200) {

                  $('#grid_movement').jqxGrid('updatebounddata');
                  //alert(data.test);
                } else {
                  alert(data.message);
                }
              });

            $(this).dialog("close");
          },
          Cancel: function() {
            $(this).dialog("close");
          }
        }

      });

    });



    $('#formNewIssue_row').on('submit', function(event) {
      //$('#modal_add').modal('hide');
      event.preventDefault();
      // $("#mi-modal").modal('show');
      // $("#modal-btn-si").on("click", function(){
      if ($('input[name=press_Batch]').val() == "" || $('input[name=ItemID]').val() == '') {
        if ($('input[name=ItemID]').val() == "" && $('input[name=press_Batch]').val() == "") {
          //  alert('กรุณาเลือก Item และ Batch');
          $('input[name=ItemID]').val('').focus();
          return false;

        }
        if ($('input[name=ItemID]').val() == "") {
          //  alert('กรุณาเลือก Item');
          $('input[name=ItemID]').val('').focus();
          return false;
        }

        if ($('input[name=press_Batch]').val() == "") {
          //  alert('กรุณาเลือก Batch');
          $('input[name=press_Batch]').val('').focus();
          return false;
        }



      }
      gojax_f('post', base_url + '/api/journal/table/saveInventJournalLine', '#formNewIssue_row')
        .done(function(data) {
          if (data.status === 200) {
            //alert(data.message);
            $('#grid_movement').jqxGrid('updatebounddata');
            $('#grid_line_row').jqxGrid('updatebounddata');
          } else {
            alert(data.message);
          }
        });
      // $("#mi-modal").modal('hide');
      $("#modal_New").modal('hide');
      //  });
      // $("#modal-btn-no").on("click", function(){
      //   $("#mi-modal").modal('hide');
      // });
    });





    $('#employee_code').on('change', function(event) {

      gojax('get', base_url + '/api/employee/' + $('#employee_code').val() + '/division')
        .done(function(data) {
          $.each(data, function(index, val) {
            open_button();
            $('#division').val(val.Description);
            $('#division_value').val(val.Code);
            //alert(val.Code);
          });
        })
        .fail(function() {
          $('#modal_alert').modal({
            backdrop: 'static'
          });
          $('#modal_alert_message').text('ไม่สามารถดึงข้อมูลได้ กรุณาลองอีกครั้ง');
          $('#top_alert').hide();
          close_button();
        });


    });

    $('#Movement_type').on('change', function(event) {

      $('#user').val('').focus();




    });

    $('#Requisition_Note').on('change', function(event) {

      $('#qty').val('1').focus();
      var Batchcheck = $('input[name=press_Batch]').val();
      var Itemcheck = $('input[name=press_item]').val();
      var tempcheck = $('input[name=TemplateSN]').val();
      var checkSNbutton = $('input[name=checkSNbutton]').val();

      if (tempcheck == "") {
        if (Batchcheck == "" || Itemcheck == "") {

          $('button#submitNewRow').prop('disabled', true);
        } else {
          $('button#submitNewRow').prop('disabled', false);
        }
      } else {
        if (Batchcheck == "" || Itemcheck == "" || checkSNbutton == "0") {

          $('button#submitNewRow').prop('disabled', true);
        } else {
          $('button#submitNewRow').prop('disabled', false);
        }
      }






    });

    // $('#user').keydown(function(event) {
    //
    //   	$('input[name=pass]').prop('readonly', false);
    //
    //   // if (event.which === 13) {
    //   //   $('#pass').val('').focus();
    //   // }
    //
    // });
    //

    gojax('get', base_url + '/ismobile').done(function(data) {
      if (data.status === 200) {
        grid_movement_mobile();
      } else {
        grid_movement_desktop();
      }
    });

    $('#print').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if (typeof rowdata !== 'undefined') {
        window.open(base_url + '/movement_issue/print/' + rowdata.InventJournalID + '?mode=' + rowdata.JournalTypeID + '&create_date=' + rowdata.CreateDate, '_blank');
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });

    $('#print_summary').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if (typeof rowdata !== 'undefined') {
        window.open(base_url + '/movement_issue/printsum/' + rowdata.InventJournalID + '?mode=' + rowdata.JournalTypeID + '&create_date=' + rowdata.CreateDate, '_blank');
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });


    $('#grid_line_row').on('rowclick', function(event) {
      var args = event.args;
      var boundIndex = args.rowindex;
      var checkauth = $('input[name=checkauth]').val();
      var checkstatusrow = $('input[name=checkstatusL]').val();

      var datarow = $("#grid_line_row").jqxGrid('getrowdata', boundIndex);
      //  alert(checkstatusrow);
      //  alert(checkauth);

      if(checkauth == 1){
        if(checkstatusrow == 1){
        $('#New_row').show();
        $('#Confirm_row').show();
        $('#Print_row').hide();
        $('#Delete_row').show();
        $('#Update_row').show();
        }
        else if (checkstatusrow == 2){
          if(datarow.Status == 1){
            $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').show();
            $('#Delete_row').show();
            $('#Update_row').show();
          }
          else if (datarow.Status == 2 || datarow.Status == 3){
            $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').show();
            $('#Delete_row').hide();
            $('#Update_row').show();
          }
        }
        else if (checkstatusrow == 3){
          $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').show();
            $('#Delete_row').hide();
            $('#Update_row').show();
        }
        else if (checkstatusrow == 6){
          $('#New_row').hide();
            $('#Confirm_row').hide();
            $('#Print_row').show();
            $('#Delete_row').hide();
            $('#Update_row').hide();
        }
      }
      else if (checkauth == 0){
        if(checkstatusrow == 1){
          $('#New_row').show();
          $('#Confirm_row').show();
          $('#Print_row').hide();
          $('#Delete_row').show();
          $('#Update_row').show();
        }
        else if (checkstatusrow == 2){
          $('#New_row').hide();
          $('#Confirm_row').hide();
          $('#Print_row').show();
          $('#Delete_row').hide();
          $('#Update_row').hide();
        }
        else if (checkstatusrow == 3){
          $('#New_row').hide();
          $('#Confirm_row').hide();
          $('#Print_row').show();
          $('#Delete_row').hide();
          $('#Update_row').show();
        }
        else if (checkstatusrow == 6){
          $('#New_row').hide();
          $('#Confirm_row').hide();
          $('#Print_row').show();
          $('#Delete_row').hide();
          $('#Update_row').hide();
        }
      }
    });

    $('#grid_movement').on('rowclick', function(event) {
      var args = event.args;
      var boundIndex = args.rowindex;
      var datarow = $("#grid_movement").jqxGrid('getrowdata', boundIndex);
      // console.log(datarow.Status);
      if (datarow.JournalTypeID == "MOV") {
        $('li > a#print_summary ').hide();
      } else {
        $('li > a#print_summary ').show();
      }
      if (datarow.Status === "Completed") {
        $('#print').show();
      } else {
        $('#print').hide();
      }

      if (datarow.Status === "Confirm") {
        $('#postback').show();
      } else {
        $('#postback').hide();
      }

    });

    $('#postback').hide();

    $('#postback').on('click', function() {
      var rowdata = row_selected('#grid_movement');

      if (typeof rowdata !== 'undefined') {
        var InventJournalID = rowdata.InventJournalID;
        //journalID_postBack
        $('#modal_postback').modal({
          backdrop: 'static'
        });
        $('.modal-title-postback').text("Confirm Authorize for PostBack Journal: " + InventJournalID);
        $("#formPostBackJournal")[0].reset();
        $('#txtJournalID').val(InventJournalID);
      }
    });

    $('#formPostBackJournal').on('submit', function(event) {
      $('#modal_postback').modal('hide');
      event.preventDefault();
      var InventJournalID = $('#txtJournalID').val();
      gojax_f('post', base_url + '/api/movement_issue/checkAuthorizePostBack', '#formPostBackJournal')
        .done(function(data) {
          if (data.status === 200) {
            gojax_f('post', base_url + '/api/movement_issue/checkStatusPostBack/' + InventJournalID)
              .done(function(data2) {
                if (data2.status === 200) {
                  gojax_f('post', base_url + '/api/movement_issue/UpdatePostBack/' + InventJournalID, '#formPostBackJournal')
                    .done(function(data3) {
                      if (data3.status === 200) {
                        $('#grid_movement').jqxGrid('updatebounddata');
                        $('#postback').hide();
                      } else {
                        alert(data3.message);
                      }
                    });
                } else {
                  alert(data2.message);
                }
              });
          } else {
            alert(data.message);
          }
        });
    });


    $('#Add').on('click', function() {


      $('#modal_add').modal({
        backdrop: 'static'
      });

      $("#formNewIssue")[0].reset();


    });

    $('#New_row').on('click', function() {
      $('#modal_New').modal({
        backdrop: 'static'
      });
      $("#formNewIssue_row")[0].reset();
      $('input[name=checkstatus]').val(0);
      $('#grid_line_row').jqxGrid('updatebounddata');
      $('#toppic').text("Create New");
      $('input[name=ItemID]').val('').focus();
      $('input[name=journal_ID_show]').val($('#journal_ID_show').val());
      $('button#submitNewRow').prop('disabled', true);
      $('input[name=TemplateSN]').val('').attr('readonly', false);
      $('#qty').prop('readonly', false);

      document.getElementById("Press_Item").disabled = false;
      document.getElementById("press_Batch_id").disabled = false;
    });

    $('#Update_row').on('click', function() {
      var rowdata = row_selected('#grid_line_row');
      var checkstatusrow = $('input[name=checkstatusL]').val();

      if (typeof rowdata !== 'undefined') {
        //alert(rowdata.Status);
        if (checkstatusrow == 2) {
          if (rowdata.Status == 1) {
            var id = $('input[name=journal_ID_show]').val();
            var item = rowdata.ItemID;
            var batch = rowdata.Batch;

            $('#modal_New').modal({
              backdrop: 'static'
            });
            $('#toppic').text("UPDATE");
            $('input[name=ItemID]').val(rowdata.ItemID).prop('readonly', true);
            $('input[name=press_Batch]').val(rowdata.Batch).prop('readonly', true);
            $('#Requisition_Note').prop('disabled', true).val(rowdata.RequsitionID);
            $('input[name=qty]').val(rowdata.QTY);
            $('input[name=TemplateSN]').val(rowdata.TemplateSerialNo);
            $('input[name=checkstatus]').val(1);
            $('input[name=ID_row]').val(rowdata.ID);
            $('input[name=status]').val(rowdata.Status);
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('button#submitNewRow').prop('disabled', true);
            $('input[name=TemplateSN]').attr('readonly', false);
            $('input[name=checkSNbutton]').val('0');
            gojax_f('post', base_url + '/api/v1/movement/batch/checkcounbatch/' + id + '/' + item + '/' + batch)
              .done(function(data) {
                if (data.status === 200) {
                  $('input[name=_qtycheck]').val(data.message)

                } else {

                }
              });



            document.getElementById("Press_Item").disabled = true;
            document.getElementById("press_Batch_id").disabled = false;

            if (rowdata.TemplateSerialNo == "") {
              $('input[name=TemplateSN]').attr('readonly', true);
              $('#qty').prop('readonly', false);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            } else {
              $('input[name=TemplateSN]').attr('readonly', false);
              $('#qty').prop('readonly', true);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            }

          }else if (rowdata.Status == 2) {
            var id = $('input[name=journal_ID_show]').val();
            var item = rowdata.ItemID;
            var batch = rowdata.Batch;

            $('#modal_New').modal({
              backdrop: 'static'
            });
            $('#toppic').text("UPDATE");
            $('input[name=ItemID]').val(rowdata.ItemID).prop('readonly', true);
            $('input[name=press_Batch]').val(rowdata.Batch).prop('readonly', true);
            $('#Requisition_Note').prop('disabled', true).val(rowdata.RequsitionID);
            $('input[name=qty]').val(rowdata.QTY);
            $('input[name=TemplateSN]').val(rowdata.TemplateSerialNo);
            $('input[name=checkstatus]').val(1);
            $('input[name=ID_row]').val(rowdata.ID);
            $('input[name=status]').val(rowdata.Status);
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('button#submitNewRow').prop('disabled', true);
            $('input[name=TemplateSN]').attr('readonly', false);
            $('input[name=checkSNbutton]').val('0');
            gojax_f('post', base_url + '/api/v1/movement/batch/checkcounbatch/' + id + '/' + item + '/' + batch)
              .done(function(data) {
                if (data.status === 200) {
                  $('input[name=_qtycheck]').val(data.message)

                } else {

                }
              });


            document.getElementById("Press_Item").disabled = true;
            document.getElementById("press_Batch_id").disabled = false;

            if (rowdata.TemplateSerialNo == "") {
              $('input[name=TemplateSN]').attr('readonly', true);
              $('#qty').prop('readonly', false);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            } else {
              $('input[name=TemplateSN]').attr('readonly', false);
              $('#qty').prop('readonly', true);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            }

          } else if (rowdata.Status == 3) {
            var id = $('input[name=journal_ID_show]').val();
            var item = rowdata.ItemID;
            var batch = rowdata.Batch;

            $('#modal_New').modal({
              backdrop: 'static'
            });
            $('#toppic').text("UPDATE");
            $('input[name=ItemID]').val(rowdata.ItemID).prop('readonly', true);
            $('input[name=press_Batch]').val(rowdata.Batch).prop('readonly', true);
            $('#Requisition_Note').prop('disabled', true).val(rowdata.RequsitionID);
            $('input[name=qty]').val(rowdata.QTY);
            $('input[name=TemplateSN]').val(rowdata.TemplateSerialNo);
            $('input[name=checkstatus]').val(1);
            $('input[name=ID_row]').val(rowdata.ID);
            $('input[name=status]').val(rowdata.Status);
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('button#submitNewRow').prop('disabled', true);
            $('input[name=TemplateSN]').attr('readonly', false);
            $('input[name=checkSNbutton]').val('0');
            gojax_f('post', base_url + '/api/v1/movement/batch/checkcounbatch/' + id + '/' + item + '/' + batch)
              .done(function(data) {
                if (data.status === 200) {
                  $('input[name=_qtycheck]').val(data.message)

                } else {

                }
              });



            document.getElementById("Press_Item").disabled = true;
            document.getElementById("press_Batch_id").disabled = false;

            if (rowdata.TemplateSerialNo == "") {
              $('input[name=TemplateSN]').attr('readonly', true);
              $('#qty').prop('readonly', false);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            } else {
              $('input[name=TemplateSN]').attr('readonly', false);
              $('#qty').prop('readonly', true);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            }

          } else {
            alert('ไม่สามารถอัพเดทได้');
          }
        } else {
          if (rowdata.Status == 1) {
            var id = $('input[name=journal_ID_show]').val();
            var item = rowdata.ItemID;
            var batch = rowdata.Batch;

            $('#modal_New').modal({
              backdrop: 'static'
            });
            $('#toppic').text("UPDATE");
            $('input[name=ItemID]').val(rowdata.ItemID);
            $('input[name=press_Batch]').val(rowdata.Batch);
            $('select[name=Requisition_Note]').val(rowdata.RequsitionID);
            $('input[name=qty]').val(rowdata.QTY);
            $('input[name=TemplateSN]').val(rowdata.TemplateSerialNo);
            $('input[name=checkstatus]').val(1);
            $('input[name=ID_row]').val(rowdata.ID);
            $('#grid_line_row').jqxGrid('updatebounddata');
            $('button#submitNewRow').prop('disabled', true);
            $('input[name=TemplateSN]').attr('readonly', false);
            $('input[name=checkSNbutton]').val('0');
            gojax_f('post', base_url + '/api/v1/movement/batch/checkcounbatch/' + id + '/' + item + '/' + batch)
              .done(function(data) {
                if (data.status === 200) {
                  $('input[name=_qtycheck]').val(data.message)

                } else {

                }
              });



            document.getElementById("Press_Item").disabled = false;
            document.getElementById("press_Batch_id").disabled = false;

            if (rowdata.TemplateSerialNo == "") {
              $('input[name=TemplateSN]').attr('readonly', true);
              $('#qty').prop('readonly', false);
              document.getElementById("Press_Item").disabled = false;
              document.getElementById("press_Batch_id").disabled = false;
            } else {
              $('input[name=TemplateSN]').attr('readonly', false);
              $('#qty').prop('readonly', true);
              document.getElementById("Press_Item").disabled = true;
              document.getElementById("press_Batch_id").disabled = true;
            }

          } else {
            alert('ไม่สามารถอัพเดทได้');
          }
        }
      } else {
        alert('select row data');
      }
    });

    $('#Delete_row').on('click', function() {
      var rowdata = row_selected('#grid_line_row');
      if (typeof rowdata !== 'undefined') {
        if (rowdata.Status == 1) {
          var id = rowdata.ID;
          var junalId = rowdata.InventJournalID;
          var check = 0;
          //  $("#mi-modal").modal('show');
          // $("#modal-btn-si").on("click", function(){
          //alert(rowdata.ID);
          var r = confirm("Are you sure!");
          if (r == true) {


            gojax_f('post', base_url + '/api/movement_issue/EditreacordLine/' + id + '/' + check + '/' + junalId)
              .done(function(data) {
                if (data.status === 200) {

                  $('#grid_line_row').jqxGrid('updatebounddata');


                } else {
                  alert(data.message);
                }
              });
          }
          $('#grid_movement').jqxGrid('updatebounddata');


        } else {
          alert('ไม่สามารถอัพเดทได้');
        }
      } else {
        alert('select row data');
      }

      // $("#mi-modal").modal('show');
      // $("#modal-btn-si").on("click", function(){
      //     gojax_f('post', base_url+'/api/journal/table/saveInventJournalLine', '#formNewIssue_row')
      //     .done(function(data) {
      //       if (data.status === 200) {
      //         //alert(data.message);
      //           $('#grid_line_row').jqxGrid('updatebounddata');
      //   } else {
      //           alert(data.message);
      //   }
      // });
      // $("#mi-modal").modal('hide');
      // $("#modal_New").modal('hide');
      // });
      // $("#modal-btn-no").on("click", function(){
      //   $("#mi-modal").modal('hide');
      // });
    });

    $('#Confirm_row').on('click', function() {
      var rowdata = row_selected('#grid_movement');

      var id = $('input[name=journal_ID_show]').val();
      var check = 1;
      var junalId = rowdata.InventJournalID;

      //  alert(id);
      //  alert(junalId);
       var r = confirm("Are you sure!");
          if (r == true) {
       //55555
      //$("#mi-modal").modal('show');
      //$("#modal-btn-si").on("click", function(){
      gojax_f('post', base_url + '/api/movement_issue/EditreacordLine/' + id + '/' + check + '/' + junalId)
        .done(function(data) {
          if (data.status === 200) {
            //alert(data.message);
            $('#grid_line_row').jqxGrid('updatebounddata');
            $("#modal_line_row").modal('hide');
            $('#grid_movement').jqxGrid('updatebounddata');
          } else {
              alert(data.message);
          }
        });
      }
      
      //  $("#mi-modal").modal('hide');
      //$("#modal_New").modal('hide');
      //  });
      //  $("#modal-btn-no").on("click", function(){
      //  $("#mi-modal").modal('hide');
      //  });


    });

    $('#Print_row').on('click', function() {
      //var rowdata = row_selected('#grid_line_row');
      var CreateDate = $('input[name=createdateIssu]').val()
      var id = $('input[name=journal_ID_show]').val();
      //alert(CreateDate);
      // if(typeof rowdata !== 'undefined') {
      //   if(rowdata.Status == 6)
      //   {
      window.open(base_url + '/movement_issue/printlist/' + id + '?create_date=' + CreateDate, '_blank');
      //   }
      //   else {
      //     alert('ไม่ไม่สามารถทำรายการได้');
      //   }
      // }
      // else {
      //    alert(กรุณาเลือกรายการ)
      // }
    });
    $('#export').on('click', function() {
      var rowdata = $('input[name=JornalId]').val();
      var check = "export";

      if (!!rowdata) {
        window.open(base_url + '/api/movementissue/report/' + rowdata + '/' + check, '_blank');
      }

    });


    $('#line').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if (typeof rowdata !== 'undefined') {
        $('#modal_line').modal({
          backdrop: 'static'
        });
        $('.modal-title').text(rowdata.InventJournalID);
        $('input[name=JornalId]').val(rowdata.InventJournalID);
        grid_line(rowdata.InventJournalID);
      }
    });

    // $('input[name=qty]').on('keydown', function(e){
    //     if(this.value <= 0){
    //        alert('You have entered more than 100 as input');
    //        return false;
    //     }
    // });
    // $('input[name=qty]').keyup(function(){
    //     if($(this).val() == "")
    //     {
    //         $('input[name=qty]').val('').focus();
    //        $('button#submitNewRow').prop('disabled', true);
    //     }
    //     else {
    //       if ($(this).val() <= 0){
    //         alert(" จำนวนเต็มที่มากกว่า 0");
    //         $('input[name=qty]').val('').focus();
    //          $('button#submitNewRow').prop('disabled', true);
    //       }
    //        $('button#submitNewRow').prop('disabled', false);
    //
    //     }
    //
    //
    //   });

    // $('input[name=TemplateSN]').keyup(function(){
    //
    //
    //   alert(1224);
    //
    //
    //   });

    $('#Press_Item').on('click', function() {
      $('#modal_Item').modal({
        backdrop: 'static'
      });
      grid_Item($('#journal_ID').val());
    });

    $('#checkSN').on('click', function() {
      var id = $('input[name=TemplateSN]').val();
      var check = $('input[name=journal_ID_show]').val();
      $('input[name=checkSNbutton]').val('1');


      gojax_f('post', base_url + '/api/movement_issue/checkSN/' + id + '/' + check)
        .done(function(data) {
          if (data.status === 200) {
            $('input[name=ItemID]').val(data.item);
            $('input[name=press_Batch]').val(data.batch);
            $('input[name=qty]').val(1);
            document.getElementById("Press_Item").disabled = true;
            document.getElementById("press_Batch_id").disabled = true;
            $('#qty').prop('readonly', true);
            $('select[name=Requisition_Note]').val('');

          } else {
            alert(data.message);
            $('input[name=TemplateSN]').val('');
            $('select[name=Requisition_Note]').val('');
            $('input[name=ItemID]').val('');
            $('input[name=press_Batch]').val('');
            document.getElementById("Press_Item").disabled = false;
            document.getElementById("press_Batch_id").disabled = false;
            $('#qty').val('').prop('readonly', false);


          }
        });




    });

    $('input[name=TemplateSN]').keyup(function() {
      if ($(this).val() == "") {
        // $('input[name=ItemID]').val('');
        // $('input[name=press_Batch]').val('');
        // $('input[name=qty]').val('');

        document.getElementById("Press_Item").disabled = false;
        document.getElementById("press_Batch_id").disabled = false;
        $('#qty').prop('readonly', false);
      } else {
        document.getElementById("Press_Item").disabled = true;
        document.getElementById("press_Batch_id").disabled = true;
        $('select[name=Requisition_Note]').val('');
      }


    });

    $('#press_Batch_id').on('click', function() {
      $('#modal_Batch').modal({
        backdrop: 'static'
      });
      grid_Batch($('#journal_ID').val(), $('#ItemID').val());
    });



    $('#grid_movement').on('dblclick', function() {
      var rowdata = row_selected('#grid_movement');



      if (typeof rowdata !== 'undefined') {
        $('#modal_line_row').modal({
          backdrop: 'static'
        });
        $('input[name=journal_ID_show]').val(rowdata.InventJournalID);
        $('input[name=Movement_By_show]').val(rowdata.Name);
        $('input[name=journal_ID]').val(rowdata.InventJournalID);
        $('input[name=createdateIssu]').val(rowdata.CreateDate);
        $('input[name=checkauth]').val(rowdata.PostbackMovement);
        $('input[name=checkstatusL]').val(rowdata.IdStatus);
      var checkauth = $('input[name=checkauth]').val();

      //  alert(checkauth);
      //  alert(rowdata.IdStatus);

      if(checkauth == 0){
        if(rowdata.IdStatus == 2 || rowdata.IdStatus == 3 || rowdata.IdStatus == 6){
          $('#New_row').hide();
          $('#Confirm_row').hide();
          $('#Print_row').show();
          $('#Delete_row').hide();
          $('#Update_row').hide();
          }
          else if (rowdata.IdStatus == 1)
          {
            $('#New_row').show();
            $('#Confirm_row').show();
            $('#Print_row').hide();
            $('#Delete_row').show();
            $('#Update_row').show();
          }
      }else if(checkauth == 1){
        if(rowdata.IdStatus == 2 || rowdata.IdStatus == 3 || rowdata.IdStatus == 6){
          $('#New_row').hide();
          $('#Confirm_row').hide();
          $('#Print_row').show();
          $('#Delete_row').hide();
          $('#Update_row').hide();
          }
          else if (rowdata.IdStatus == 1)
          {
            $('#New_row').show();
            $('#Confirm_row').show();
            $('#Print_row').hide();
            $('#Delete_row').show();
            $('#Update_row').show();
          }
      }
      
            
        // if(rowdata.IdStatus == 6)
        // {
        //   $('#Print_row').hide();
        // }
        grid_line_row(rowdata.InventJournalID);
        var requ = rowdata.JournalTypeID;


        gojax('get', base_url + '/api/movement_type/Requisitionlist/' + requ)
          .done(function(data) {
            $('#Requisition_Note').html('<option value="">= กรุณาเลือก =</option>');
            $.each(data, function(index, el) {
              $('#Requisition_Note').append('<option value="' + el.ID + '">' + el.Description + '</option>');
            });
          });
      }

    });

    $('#grid_Item').on('dblclick', function() {
      var rowdata = row_selected('#grid_Item');

      if (typeof rowdata !== 'undefined') {
        $('input[name=ItemID]').val(rowdata.ITEM);
        $('#modal_Item').modal('hide');
        $('input[name=press_Batch]').val('').focus();
        $('input[name=TemplateSN]').val('').attr('readonly', true);
        $('#qty').prop('readonly', false);
        $('select[name=Requisition_Note]').val('');

      }
    });

    $('#grid_Batch').on('dblclick', function() {
      var rowdata = row_selected('#grid_Batch');
      if (typeof rowdata !== 'undefined') {
        $('input[name=press_Batch]').val(rowdata.BATCH);
        $('input[name=_qtycheck]').val(rowdata.TOTAL)
        $('input[name=qty]').val('');
        $('#modal_Batch').modal('hide');
      }
    });

    $('#newIssue').on('click', function(event) {
      event.preventDefault();
      window.location = base_url + '/movement/issue/new';
    });

    $('#selectEmp').on('click', function(event) {
      event.preventDefault();

      var rowdata = row_selected('#grid_movement');
      if (typeof rowdata !== 'undefined') {
        window.location = base_url + '/movement/issue/new?j=' + rowdata.InventJournalID;
      }
    });


  });

  function grid_line(journalId) {

    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'BarcodeID',
          type: 'string'
        },
        {
          name: 'CuringCode',
          type: 'string'
        },
        {
          name: 'RN',
          type: 'string'
        },
        {
          name: 'CreateBy',
          type: 'string'
        },
        {
          name: 'CreateDate',
          type: 'date'
        }
      ],
      url: base_url + '/api/movement_issue/' + journalId + '/latest'
    });

    return $("#grid_line").jqxGrid({
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
          text: "No.",
          width: 50,
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
          }
        },
        {
          text: 'Barcode',
          datafield: 'BarcodeID',
          width: 150
        },
        {
          text: 'Curing Code',
          datafield: 'CuringCode',
          width: 150
        },
        {
          text: 'Requsition Note',
          datafield: 'RN',
          width: 200
        },
        {
          text: 'Create By',
          datafield: 'CreateBy',
          width: 150
        },
        {
          text: 'Create Date',
          datafield: 'CreateDate',
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss',
          width: 150
        }
      ]
    });
  }

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
          name: 'RN',
          type: 'string'
        },
        {
          name: 'NameTH',
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
          text: "No.",
          width: 50,
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
          }
        },
        // { text: 'InventJournalID', datafield: 'InventJournalID', width: 150},
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
          width: 150
        },
        {
          text: 'Batch',
          datafield: 'Batch',
          width: 100
        },
        {
          text: 'Requisition Note',
          datafield: 'RN',
          width: 200
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
          width: 80
        }

      ]
    });
  }

  function grid_Batch(journal_id, item_id) {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'BATCH',
          type: 'string'
        },
        {
          name: 'TOTAL',
          type: 'number'
        }
      ],
      // url: base_url + '/api/movement_issue/Batch'
      url: base_url + '/api/v1/movement/batch/available/' + journal_id + '/' + item_id
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
        {
          text: 'Batch',
          datafield: 'BATCH',
          width: 100
        },
        {
          text: 'Total',
          datafield: 'TOTAL',
          width: 100
        }


      ]
    });
  }

  function grid_Item(journal_id) {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'ITEM',
          type: 'string'
        },
        {
          name: 'ITEM_NAME',
          type: 'string'
        },
        {
          name: 'BATCH',
          type: 'string'
        },
        {
          name: 'TOTAL',
          type: 'number'
        }

      ],
      // url: base_url + '/api/movement_issue/item' + journal_id
      url: base_url + '/api/v1/movement/item/available/' + journal_id
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
        {
          text: 'Item ID',
          datafield: 'ITEM',
          width: 100
        },
        {
          text: 'Item Name',
          datafield: 'ITEM_NAME'
        },
        // { text: 'Batch', datafield: 'BATCH', width: 100},
        // { text: 'Total', datafield: 'TOTAL', width: 100}

      ]
    });
  }

  function grid_employee() {
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
          name: 'Name',
          type: 'string'
        },
        {
          name: 'DivisionCode',
          type: 'string'
        },
        {
          name: 'Department',
          type: 'string'
        }
      ],
      url: base_url + "/api/employee/all/by_status"
    });

    return $("#grid_employee").jqxGrid({
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
          datafield: 'Code',
          width: 100
        },
        {
          text: 'Name',
          datafield: 'Name',
          width: 220
        },
        {
          text: 'Division Code',
          datafield: 'DivisionCode',
          width: 100
        },
        {
          text: 'Department',
          datafield: 'Department',
          width: 100
        }
      ]
    });
  }

  function grid_movement_mobile() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'InventJournalID',
          type: 'string'
        },
        {
          name: 'EmpCode',
          type: 'string'
        },
        {
          name: 'Department',
          type: 'string'
        },
        {
          name: 'Division',
          type: 'string'
        },
        {
          name: 'Name',
          type: 'string'
        },
        {
          name: 'CreateBy',
          type: 'string'
        },
        {
          name: 'CreateDate',
          type: 'date'
        }
      ],
      url: base_url + "/api/movement_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          text: 'Journal ID',
          datafield: 'InventJournalID',
          width: 100
        },
        {
          text: 'Name',
          datafield: 'Name',
          width: 200
        },
        {
          text: 'Department',
          datafield: 'Division',
          width: 100
        },
        {
          text: 'Create By',
          datafield: 'CreateBy',
          width: 120
        },
        {
          text: 'Create Date',
          datafield: 'CreateDate',
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss',
          width: 150
        }
      ]
    });
  }

  function grid_movement_desktop() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'InventJournalID',
          type: 'string'
        },
        {
          name: 'JournalTypeID',
          type: 'string'
        },
        {
          name: 'EmpCode',
          type: 'string'
        },
        {
          name: 'Department',
          type: 'string'
        },
        {
          name: 'Division',
          type: 'string'
        },
        {
          name: 'Name',
          type: 'string'
        },
        {
          name: 'Status',
          type: 'string'
        },
        {
          name: 'CreateBy',
          type: 'string'
        },
        {
          name: 'CreateDate',
          type: 'datetime'
        },
        {
          name: 'CompleteBy',
          type: 'string'
        },
        {
          name: 'CompleteDate',
          type: 'date'
        },
        {
          name: 'IdStatus',
          type: 'string'
        },
        {
          name: 'PostbackMovement',
          type: 'int'
        },
        {
          name: 'Description',
          type: 'string'
        }


      ],
      url: base_url + "/api/movement_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          text: 'Journal ID',
          datafield: 'InventJournalID',
          width: 100
        },
        {
          text: 'Journal Type',
          datafield: 'Description',
          width: 250
        },
        {
          text: 'Name',
          datafield: 'Name',
          width: 200
        },
        {
          text: 'Department',
          datafield: 'Division',
          width: 100
        },
        {
          text: 'Status',
          datafield: 'Status',
          width: 100,
          filtertype: 'checkedlist'
        },
        {
          text: 'Create By',
          datafield: 'CreateBy',
          width: 120
        },
        {
          text: 'Create Date',
          datafield: 'CreateDate',
          filtertype: 'range',
          columntype: 'datetimeinput',
          cellsformat: 'yyyy-MM-dd HH:mm:ss',
          width: 150
        },
        {
          text: 'Complete By',
          datafield: 'CompleteBy',
          width: 120
        },
        {
          text: 'Complete Date',
          datafield: 'CompleteDate',
          width: 150,
          cellsformat: 'yyyy-MM-dd HH:mm:ss'
        }

      ]
    });
  }

  function grid_requsition() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [{
          name: 'ID',
          type: 'number'
        },
        {
          name: 'Description',
          type: 'string'
        }
      ],
      url: base_url + "/api/requsition_note/all"
    });

    return $("#grid_requsition").jqxGrid({
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
          width: 200
        }
      ]
    });
  }

  function NumChk() {
    var number = document.getElementById("qty").value;
    if (number <= 0) {
      //  alert(1234);
    }
  }
</script>