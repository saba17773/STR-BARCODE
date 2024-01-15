<?php $this->layout("layouts/base", ['title' => 'Warehouse Counting']); ?>

<h1>Setup</h1>

<form id="form_setup_counting" style="width: 400px;">
  <div class="form-group">
    <label for="minimum_batch">Batch</label>
    <input type="text" name="minimum_batch" class="form-control" value="<?php echo $option["minimum_batch"]; ?>">
  </div>

  <div class="form-group">
    <label for="minimum_date">Date</label>
    <input type="text" name="minimum_date" readonly class="form-control" value="<?php echo $option["minimum_date"]; ?>">
  </div>

  <?php
  $useDate = "";
  $useBatch = "";
  if ((int)$option["use_date"] === 1) $useDate = "checked";
  if ((int)$option["use_batch"] === 1) $useBatch = "checked";
  ?>
  <div class="form-group">
    <label for="use_batch"><input type="checkbox" name="use_batch" id="use_batch" value="1" <?php echo $useBatch; ?>> ใช้เงื่อนไข Batch < <?php echo $option["minimum_batch"]; ?></label> <br>
        <label for="use_date"><input type="checkbox" name="use_date" id="use_date" value="1" <?php echo $useDate; ?>> ใช้เงื่อนไข Warehouse Receive Date < <?php echo $option["minimum_date"]; ?></label> <br>
  </div>

  <div class="form-group">
    <button type="submit" class="btn btn-primary">Save</button>

      <?php if (date("Ymd") === "20210516"): ?>
    <button type="button" class="btn btn-danger" style="margin-left: 40px;" onclick="return removeBarcode();">  Remove Old Barcode</button>
  <?php endif;?>
  </div>
</form>


<script>
  $(document).ready(function() {

    $("input[name=minimum_date]").datepicker({
      dateFormat: "yy-mm-dd"
    });

    $("form#form_setup_counting").on("submit", function(e) {
      e.preventDefault();

      $.ajax({
        url: "/warehouse_counting/setup/save",
        type: "post",
        dataType: "json",
        cache: false,
        data: $("form#form_setup_counting").serialize(),
        success: function(res, status, xhr) {
          alert(res.message);
        }
      });
    });
  });

  function removeBarcode() {
    if (confirm("Are you sure to remove barcode counting from database?")) {
        $.ajax({
          url: "/warehouse_counting/remove_barcode",
          type: "post",
          dataType: "json",
          success: function (res) {
            alert(res.message);
          }
        });
    }
  }
</script>