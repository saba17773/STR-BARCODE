<?php $this->layout("layouts/base", ['title' => 'Component']);

use App\Services\ComponentService;
$getunit    = (new ComponentService)->getUnitData();
$getsection = (new ComponentService)->getSectionData();

$unitgood   = $getunit[0]['UnitGood'];
$unitscrap  = $getunit[0]['UnitScrap'];
?>

<style type="text/css">
  td {
    padding: 10px;
    text-align: right;
  }

  .inner {
    padding: 2px;
    text-align: left;
  }

  .tdunit {
    padding: 10px;
    text-align: left;
  }

  input[type="text"] {
    font-size: 20px;
    font-weight: bold;
  }

  input[type="number"] {
    font-size: 20px;
    font-weight: bold;
  }
</style>

<h1 class="head-text">
  Component
  <br>
  <?php if($getsection) {?>
  <b style="font-size: 0.5em;">(
    <?php 
      $lastSection   = end($getsection);
      $countSection  = count($getsection);
      if ($countSection=13) {
        echo "All";
      }else{
        foreach ($getsection as $data) {
          if ($countSection<=1) {
            echo $data['SectionName'];
          }else{
            if ($data['SectionName'] !== $lastSection[0]) {
              echo $data['SectionName'].",";
            }else{
              echo $data['SectionName'];
            }
            
          }
        }
      }
    ?> )
  </b>
  <?php } ?>
</h1>

<div class="panel panel-default form-center">
  <div class="panel-body">

    <form id="form_create" onsubmit="return submit_create()">
      <table align="center">
        <tr>
          <td>
            <h3>
              <b>วันที่</b>
            </h3>
          </td>
          <td>
            <div class="row">
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" class="form-control input-lg" name="date_component" id="date_component" required placeholder="เลือกวันที่..."
                    autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-info btn-lg" id="date_component_show" type="button">
                      <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                  </span>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <h3>
              <b>Shift</b>
            </h3>
          </td>
          <td>
            <div style="text-align: left;">
              <label class="radio-inline">
                <input type="radio" name="shift" value="1" style="width: 1.5em; height: 1.5em;">
                <span style="padding-left: 10px; font-size: 1.4em;">
                  <b>กะทำงาน กลางวัน</b>
                </span>
              </label>
              <br>
              <label class="radio-inline">
                <input type="radio" name="shift" value="2" style="width: 1.5em; height: 1.5em;">
                <span style="padding-left: 10px; font-size: 1.4em;">
                  <b>กะทำงาน กลางคืน</b>
                </span>
              </label>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <h3>
              <b>Code</b>
            </h3>
          </td>
          <td>
            <input style="height: 2.5em; width: 15em;" class="form-control inputs" type="text" name="item" id="item" autocomplete="off"
              required>
            <input type="hidden" name="pastcode" id="pastcode" readonly>
          </td>
          <td colspan="2" align="left">
            <h4>
              <p id="item_noexist"></p>
            </h4>
          </td>
        </tr>
        <tr>
          <td>
            <h3>
              <b>Qty</b>
            </h3>
          </td>
          <td>
            <input style="height: 2.5em; width: 15em;" class="form-control inputs" type="number" name="qty" id="qty" autocomplete="off"
              required>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <button type="submit" class="btn btn-lg btn-primary">
              <i class="glyphicon glyphicon-save"></i> บันทึก</button>
            <button type="reset" id="reset" class="btn btn-lg btn-default">
              <i class="glyphicon glyphicon-refresh"></i> ล้างข้อมูล</button>
          </td>
          <td>
          </td>
        </tr>
      </table>

      <div>
        <label>
          <h4>
            <p id="submit_pastcode"></p>
            <p id="submit_date"></p>
          </h4>
        </label>
      </div>

    </form>

  </div>
</div>

<!-- dialog postcode -->
<div class="modal" id="modal_defect" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title">สาเหตุ</h4>
      </div>
      <div class="modal-body">
        <form id="form_defect">
          <div class="form-group">
            <div id="griddefect"></div>
          </div>
      </form>
    </div>
  </div>
</div>

  <script type="text/javascript">
    jQuery(document).ready(function ($) {

      $("#date_component").datepicker({ dateFormat: 'dd-mm-yy', changeYear: true });

      $('#date_component_show').click(function () {
        $('#date_component').datepicker('show');
      });

      var time_set = '<?php echo date('H: i'); ?>';

      if (time_set >= '08:01' && time_set <= '20:00') {
        $("input[name=shift][value='1']").attr('checked', 'true');
      } else {
        $("input[name=shift][value='2']").attr('checked', 'true');
      }

      $('#btn_scrap').on('click', function () {

        $('#modal_defect').modal({ backdrop: 'static' });
        griddefect();
        $('#griddefect').on('rowdoubleclick', function (event) {
          var args = event.args;
          var boundIndex = args.rowindex;
          var datarow = $("#griddefect").jqxGrid('getrowdata', boundIndex);
          $('#defectid').val(datarow.DefectID);
          $('#modal_defect').modal('hide');

        });
      });

      $('#reset').on('click', function () {
        $('#item').focus();
      });

    });

    function griddefect() {

      var dataAdapter = new $.jqx.dataAdapter({
        datatype: "json",
        datafields: [
          { name: "GroupID", type: "int" },
          { name: "GroupName", type: "string" },
          { name: "GroupDescriptiion", type: "string" },
          { name: "GroupDescriptionDetail", type: "string" },
          { name: "DefectID", type: "string" }
        ],
        url: '/component/defect'
      });

      return $("#griddefect").jqxGrid({
        width: '100%',
        source: dataAdapter,
        pageable: true,
        autoHeight: true,
        filterable: true,
        showfilterrow: true,
        enableanimations: false,
        columnsresize: true,
        sortable: true,
        pagesize: 10,
        columns: [
          { text: "Group Scrap", datafield: "GroupName", width: '20%' },
          { text: "Descriptiion", datafield: "GroupDescriptiion", width: '50%' },
          { text: "DescriptionDetail", datafield: "GroupDescriptionDetail" }
        ]
      });
    }

    // $("#qty").keypress(function (e) {
    //   var character = String.fromCharCode(e.keyCode)
    //   var newValue = this.value + character;
    //   if (isNaN(newValue) || hasDecimalPlace(newValue, 2)) {
    //       e.preventDefault();
    //       return false;
    //   }
    // });

    // function hasDecimalPlace(value, x) {
    //     var pointIndex = value.indexOf('.');
    //     return  pointIndex >= 0 && pointIndex < value.length - x;
    // }

    $('#item').keydown(function (event) {
      if (event.which === 13) {
        var section = '<?php echo $_SESSION["user_componentsection"]; ?>';
        var item = $('#item').val();

        if (section == '') {
          document.getElementById("item_noexist").style.color = "red";
          document.getElementById("item_noexist").innerHTML = "ไม่พบโค๊ดนี้";
          $('#item_noexist').show();
          $('#item').val("");
          $('#item').focus();
        } else {

          $('#pastcode').val(item);
          gojax('get', base_url + '/component/pastcode?item=' + item)
            .done(function (data) {
              if (data.length > 0) {
                $.each(data, function (index, val) {
                  $('#item').val(val.PastCodeID);
                  $('#item_noexist').hide();
                });
              } else {
                document.getElementById("item_noexist").style.color = "red";
                document.getElementById("item_noexist").innerHTML = "ไม่พบโค๊ดนี้";
                $('#item_noexist').show();
                $('#item').val("");
                $('#item').focus();
              }
            });

        }

      }
    });

    function submit_create() {

      if ($("input[name=shift]:checked").val() == 1) {
        var shift = 1;
      } else if ($("input[name=shift]:checked").val() == 2) {
        var shift = 2;
      }

      $('#date_component').prop('readonly', true);
      $('#item').prop('readonly', true);
      $('#qty').prop('readonly', true);

      gojax('post', '/component/insert/barcode', {
        pastcode: $.trim($('#item').val()),
        item: $.trim($('#pastcode').val()),
        qty: $.trim($('#qty').val()),
        date_component: $.trim($('#date_component').val()),
        shift: shift
      }).done(function (data) {
        var submit_date = '';

        if (data.status == 200) {
          $('#date_component').prop('readonly', false);
          $('#item').prop('readonly', false);
          $('#qty').prop('readonly', false);
          var datetime = getdatetime();
          var submit_pastcode = data.pastcode;
          document.getElementById("submit_date").style.color = "green";
          document.getElementById("submit_pastcode").style.color = "green";
          document.getElementById("submit_date").innerHTML = "บันทึกครั้งล่าสุด " + datetime;
          document.getElementById("submit_pastcode").innerHTML = "Pastcode : " + submit_pastcode;
          $('#defect_noexist').hide();
          $('#pastcode').val("");
          $('#item').val("");
          $('#qty').val("");
          $('#date_component').val(data.date_return);
          $('#item').focus();

        } else if (data.status == 404) {

          $('#date_component').prop('readonly', false);
          $('#item').prop('readonly', false);
          $('#qty').prop('readonly', false);

          if (data.message == 'code_null') {
            $('#item_noexist').show();
            $('#pastcode').val("");
            $('#item').val("");
            $('#item').focus();
            document.getElementById("item_noexist").style.color = "red";
            document.getElementById("item_noexist").innerHTML = "ไม่พบโค๊ดนี้";
          } else if(data.message == 'status_completed') {
            var datetime = getdatetime();
            document.getElementById("submit_date").style.color = "red";
            document.getElementById("submit_pastcode").style.color = "red";
            document.getElementById("submit_date").innerHTML = "ไม่สามารถบันทึกได้ เนื่องจากดำเนินการ Complete เสร็จสิ้น";
            document.getElementById("submit_pastcode").innerHTML = "วันที่ : " + datetime + " กะ : " +data.shiftname;
            $('#defect_noexist').hide();
            $('#pastcode').val("");
            $('#item').val("");
            $('#qty').val("");
            // $('#date_component').val(data.date_return);
            $('#date_component').focus();
          } else if (data.message == 'qty_minus') {
            $('#item_noexist').show();
            $('#qty').focus();
            var submit_pastcode = data.pastcode;
            document.getElementById("submit_date").style.color = "red";
            document.getElementById("submit_pastcode").style.color = "red";
            document.getElementById("submit_date").innerHTML = "QTY ติดลบเกินจำนวน";
            document.getElementById("submit_pastcode").innerHTML = "Pastcode : " + submit_pastcode;
          }else{
            $('#form_create').trigger('reset');
            $('#item').focus();
            var submit_pastcode = data.pastcode;
            document.getElementById("submit_date").style.color = "red";
            document.getElementById("submit_pastcode").style.color = "red";
            document.getElementById("submit_date").innerHTML = "ไม่สามารถบันทึกได้";
            document.getElementById("submit_pastcode").innerHTML = "Pastcode : " + submit_pastcode;
          }

        }

      });

      return false;
    }

    function getdatetime() {
      var currentTime = new Date(),
        hours = currentTime.getHours(),
        minutes = currentTime.getMinutes();

      var currentDate = new Date(),
        day = currentDate.getDate(),
        month = currentDate.getMonth() + 1,
        year = currentDate.getFullYear();

      if (minutes < 10) {
        minutes = "0" + minutes;
      }

      return (day + "/" + month + "/" + year) + " " + (hours + ":" + minutes);
    }

  </script>