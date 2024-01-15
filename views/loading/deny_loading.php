<?php $this->layout("layouts/base", ['title' => 'Deny Loading']); ?>

<h1 class="text-center" style="margin-bottom: 20px;">Deny Loading Setup</h1>

<form id="form_add_deny_loading" style="max-width: 400px; margin: auto;">

  <div class="row">
    <div class="col-xs-6">
      <div class="form-group">
        <label for="">Item Id</label>
        <input type="text" id="in-itemid" name="item_id" class="form-control">
      </div>
    </div>
    <div class="col-xs-6">
      <div class="form-group">
        <label for="">Batch</label>
        <input type="text" name="batch" class="form-control">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-6">
      <label for="">Customer Group</label>
      <select name="customer_group" id="customer_group" class="form-control">
        <option value="">-- เลือกรายการ --</option>
        <option value="DOM">DOM</option>
        <option value="OVS">OVS</option>
      </select>
    </div>
    <div class="col-xs-6">
      <label for="">Check Customer Group</label>
      <label for="check_customer_group">
        <input type="checkbox" name="check_customer_group" id="check_customer_group" value="1">
        Active
      </label>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-6">
        <button type="submit" class="btn btn-success" style="margin: 20px auto;">
          บันทึกข้อมูล
        </button>
    </div>
    <div class="col-xs-6">
        <a href="/loading/export" class="btn btn-primary" style="margin: 20px auto;">
        <span class="glyphicon glyphicon-save-file"></span> Export Excel
</a>
    </div>
  </div>
  </form>

<table class="table" style="max-width: 700px; margin: auto;">
  <thead>
    <tr>
      <th>Item Id</th>
      <th>Batch</th>
      <th>Customer Group</th>
      <th>Check Customer Group</th>
      <th>#</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($data as $key => $value) { ?>
      <tr>
        <td><?php echo $value["ItemId"] ?></td>
        <td><?php echo $value["Batch"] ?></td>
        <td><?php echo $value["CustomerGroup"] ?></td>
        <td><?php echo (int)$value["CheckCustomerGroup"] === 1 ? "<span class='label label-success'>Yes</span>" : "<span class='label label-danger'>No</span>"; ?></td>
        <td><a href="javascript:void()" onclick="return deleteDenyLoading(<?php echo $value["Id"]; ?>)" class="text-danger">ลบรายการ</a></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<script>
  $(document).ready(function() {

    $("#form_add_deny_loading").on("submit", function(e) {
      e.preventDefault();
      if ($("#in-itemid").val() == '' ) {
        alert('กรุณากรอกข้อมูลให้ครบ');
        return false;
      }
      $.ajax({
        url: "/loading/add_deny_loading",
        type: "post",
        dataType: "json",
        cache: false,
        data: $("#form_add_deny_loading").serialize(),
        success: function(res) {
          $("#form_add_deny_loading").trigger("reset");
          $("button[type=submit]").prop("disabled", true).text("กำลังบันทึกข้อมูล");
          window.location = "/loading/deny";
        }
      });
    });
  });

  function deleteDenyLoading(id) {
    if (confirm("Are you sure?")) {
      $.ajax({
        url: "/loading/delete_deny_loading",
        type: "post",
        dataType: "json",
        data: {
          id: id
        },
        success: function(res) {
          if (res.result === false) {
            alert(res.message);
          } else {
            window.location = "/loading/deny";
          }
        }
      });
    }

    return false;
  }
</script>