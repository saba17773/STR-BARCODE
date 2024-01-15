<?php $this->layout("layouts/base", ['title' => 'Except Rate']); ?>

<div class="head-space"></div>

<div style="margin: 0 auto; max-width: 400px;">
  <h1 style="margin-bottom: 30px;">ไม่คำนวณค่าเรท</h1>
  <form id="formUpdateExceptRate">
    <div class="form-group">
      <label for="">ยิง Barcode ที่นี่</label>
      <input
        type="text"
        name="barcode"
        class="form-control"
        autofocus
        autocomplete="off"
        required
      />
      <span id="result" style="font-weight: bold;"></span>
    </div>
  </form>
</div>

<script>
  $(document).ready(function() {
    $("#formUpdateExceptRate").on("submit", function(e) {
      e.preventDefault();
      gojax_f("post", "/rate/except/save", "#formUpdateExceptRate").done(
        function(data) {
          $("#formUpdateExceptRate").trigger("reset");

          $("#result").html(data.message);
        }
      );
    });
  });
</script>
