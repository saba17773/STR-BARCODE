<?php $this->layout("layouts/base", ['title' => 'Warehouse Counting']); ?>

<h1 class="text-center">Warehouse Counting</h1>

<h3 style="text-align: center;" id="item_remain"></h3>

<div style="max-width: 480px; margin: auto;">
  <form id="form_warehouse_counting">
    <div class="form-group">
      <label for="">Item</label>
      <select name="item" id="item" class="form-control"></select>
    </div>

    <div class="form-group">
      <label for="">Barcode</label>
      <input type="text" name="barcode" class="form-control">
    </div>
  </form>
  <p id="submit_result"></p>
</div>

<!-- modal confirm barcode -->
<div class="modal fade" id="modal_confirm" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Confirm Barcode</h4>
      </div>
      <div class="modal-body">
        <form id="form_confirm_barcode">
          <p>
            <b>Item : </b> <span id="barcode_item"></span>
          </p>
          <p>
            <b>Batch : </b> <span id="barcode_batch"></span>
          </p>
          <p>
            <b>Brand : </b> <span id="barcode_brand"></span>
          </p>

          <input type="hidden" name="barcode">

          <div style="margin: 20px 0px;">
            <button type="submit" id="confirm_barcode" class="btn btn-success btn-lg" style="margin-right: 20px;">Confirm</button>
            <button type="button" id="cancel_barcode" class="btn btn-danger btn-lg">Cancel</button>
          </div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  $(document).ready(function() {

    // variable
    var loopCheck;

    $("#item").select2({
      filter: true
    });

    /// loop count remain item in warehouse
    var timerCheck = setInterval(function() {
      $.ajax({
        url: "/warehouse_counting/get_remain_item",
        type: "post",
        dataType: "json",
        cache: false,
        data: {
          item: $("#item").val()
        },
        success: function(res, status, xhr) {
          // consolw.log(res.remain);
          if ($("#item").val() !== "") {
            $("#item_remain").html("Item " + $("#item").val() + " เหลืออยู่ " + res.remain + " เส้น");
          } else {
            $("#item_remain").html("");
          }
        }
      });
    }, 5000);

    // get item dropdown when page initialized
    $.ajax({
      url: "/warehouse_counting/get_item",
      type: "get",
      dataType: "json",
      cache: false,
      success: function(res, status, xhr) {
        $("#item").html("<option value=''>--- เลือก ---</option>");
        $.each(res, function(i, v) {
          $("#item").append("<option value='" + v.ItemID + "'>" + v.ItemID + "</option>");
        });
      }
    });

    // close alert modal and set input barcode on focus
    $('#modal_alert').on('hidden.bs.modal', function() {
      $("#form_warehouse_counting input[name=barcode]").val('').focus();
    });

    // close warning modal and set input barcode on focus
    $('#modal_warning').on('hidden.bs.modal', function() {
      $("#form_warehouse_counting input[name=barcode]").val('').focus();
    });

    // cancel barcode
    $("#cancel_barcode").on("click", function() {
      $("#form_confirm_barcode").trigger("reset");
      $('#modal_confirm').modal("hide");

      $("#form_warehouse_counting input[name=barcode]").val("").focus();
    });

    // on change
    $("#item").on("select2:select", function(e) {
      if ($("#item").val() === "") {
        // get item again
        $.ajax({
          url: "/warehouse_counting/get_item",
          type: "get",
          dataType: "json",
          cache: false,
          success: function(res, status, xhr) {
            $("#item").html("<option value=''>--- เลือก ---</option>");
            $.each(res, function(i, v) {
              $("#item").append("<option value='" + v.ItemID + "'>" + v.ItemID + "</option>");
            });
          }
        });
      }

      // focus on input barcode
      $("input[name=barcode]").val("").focus();
    });

    // submit confirm
    $("#form_confirm_barcode").on("submit", function(e) {
      e.preventDefault();
      var bcValue = $("#form_warehouse_counting input[name=barcode]").val();
      $("#form_confirm_barcode input[name=barcode]").val(bcValue);

      $("#form_warehouse_counting").LoadingOverlay("show");

      $.ajax({
        url: "/warehouse_counting/save",
        type: "post",
        dataType: "json",
        cache: false,
        data: $("#form_confirm_barcode").serialize(),
        success: function(res, status, xhr) {
          $("#form_warehouse_counting").LoadingOverlay("hide", true);
          $('#modal_confirm').modal("hide");
          $("#form_warehouse_counting input[name=barcode]").val("").focus();

          if (res.result === false) {
            $("#submit_result").html("");

            $('#top_alert').hide();

            if (res.color === "yellow") {
              $('#modal_warning').modal();
              $('#modal_warning_message').text("(" + bcValue + ") " + res.message);
            } else {
              $('#modal_alert').modal();
              $('#modal_alert_message').text("(" + bcValue + ") " + res.message);
            }
          } else {
            $('#top_alert').show();
            $('#top_alert_message').html("(" + bcValue + ") " + res.message).css({
              "font-weight": "bold"
            });
          }
        }
      });
    });

    // barcode entered
    $("input[name=barcode]").on("keyup", function(e) {
      if (e.which === 13) { // enter
        $("#form_warehouse_counting").LoadingOverlay("show");

        $.ajax({
          url: "/warehouse_counting/get_data",
          type: "post",
          dataType: "json",
          data: $("#form_warehouse_counting").serialize(),
          success: function(res, status, xhr) {

            $("#form_warehouse_counting").LoadingOverlay("hide", true);

            if (res.result === false) {
              $("#form_warehouse_counting input[name=barcode]").val("").focus();
              $("#submit_result").html("");

              $('#top_alert').hide();

              if (res.color === "yellow") {
                $('#modal_warning').modal();
                $('#modal_warning_message').text(res.message);
              } else {
                $('#modal_alert').modal();
                $('#modal_alert_message').text(res.message);
              }
            } else {
              $("#form_confirm_barcode").trigger("reset");

              $('#modal_confirm').modal({
                backdrop: "static"
              });

              $("#barcode_item").html(res.data[0].NameTH);
              $("#barcode_batch").html(res.data[0].Batch);
              $("#barcode_brand").html(res.data[0].Brand);
            }
          }
        });
      }
    });

    // form submit
    $("#form_warehouse_counting").on("submit", function(e) {
      e.preventDefault();
    });
  });
</script>