<?php $this->layout("layouts/base", ['title' => 'Generate Component Tag']); ?>

<h1 class="text-center">
  Generate Component Tag
</h1>

<hr>

<form style="margin: 0 auto; max-width: 400px;" id="form_gen_tag">
  <div class="form-group">
    <label> เลือกวัน </label>
    <select name="days" id="days" class="form-control">
      <option value="mon">วันจันทร์</option>
      <option value="tue">วันอังคาร</option>
      <option value="wed">วันพุธ</option>
      <option value="thu">วันพฤหัสบดี</option>
      <option value="fri">วันศุกร์</option>
      <option value="sat">วันเสาร์</option>
      <option value="sun">วันอาทิตย์</option>
    </select>
  </div>

  <div class="form-group">
    <label> ตัวอย่าง </label>
    <div class="well well-sm">
      <div id="preview" style="font-size: 1.2em; font-weight: bold;">XXXX</div>
    </div>
  </div>

  <div class="form-group">
    <input type="number" name="qty" id="qty" class="form-control inputs" autocomplete="off"  required>
  </div>

  <div class="form-group">
    <button class="btn btn-primary btn-lg btn-block" type="button" id="print_tag">Print</button>
  </div>
</form>

<script>
jQuery(document).ready(function($) {

  $("#qty").on("input", function() {
    if (/^0/.test(this.value)) {
      this.value = this.value.replace(/^0/, "");
    }
  });

  $('#print_tag').on('click', function() {

    window.open("/component_tag/"+$('#days').val()+"/"+$('#qty').val());
    $('#qty').val('');
  });

  $('#form_gen_tag').submit(function(e) {
    e.preventDefault();
  });

  setInterval(function() {
    getLastestNumberByDate();
  }, 1500);
});

function getLastestNumberByDate() {
  gojax('post', '/api/v2/component/get_component_tag_last_number', {
    print_date: $('#days').val()
  }).done(function(data) {
    $('#preview').html(data.current + data.qty);
  });
}
</script>

