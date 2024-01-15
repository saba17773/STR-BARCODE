<?php $this->layout("layouts/base", ['title' => 'Curetire Code']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Curetire Code Master</div>
  <div class="panel-body">
    <div class="btn-panel">

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'create_cure_tire_code') === true) : ?>
        <button onclick="return modal_create_open()" class="btn btn-success" data-backdrop="static" data-toggle="modal" data-target="#modal_curetire_create"><span class="glyphicon glyphicon-plus"></span> Create</button>
      <?php endif ?>

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'edit_cure_tire_code') === true) : ?>
        <button class="btn btn-info" id="edit"><span class="glyphicon glyphicon-edit"></span> Edit</button>
      <?php endif ?>

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'print_cure_tire_code') === true) : ?>
        <button class="btn btn-default" id="print"><span class="glyphicon glyphicon-print"></span> Print</button>
      <?php endif ?>

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'import_cure_code') === true) : ?>
        <a href="<?php echo APP_ROOT; ?>/import/curecode" class="btn btn-primary" id="import_curecode"><span class="glyphicon glyphicon-import"></span> Import Cure Code</a>
      <?php endif ?>

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'import_topturn_code') === true) : ?>
        <a href="<?php echo APP_ROOT; ?>/import/topturn" class="btn btn-warning" id="import_topturn"><span class="glyphicon glyphicon-import"></span> Import Top Turn</a>
      <?php endif ?>

      <?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'update_item_q') === true) :
      ?>
        <a href="<?php echo APP_ROOT; ?>/import/topturn" class="btn btn-danger" id="import_topturn">
          <span class="glyphicon glyphicon-download"></span>
          Update Item Q
        </a>
      <?php endif;
      ?>
    </div>

    <div id="grid_cure"></div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal" id="modal_curetire_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
        <form id="form_create_curetire">
          <div class="form-group">
            <label for="id_name">ID</label>
            <input type="text" name="id_name" id="id_name" class="form-control" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label for="des_name">Description</label>
            <input type="text" name="des_name" id="des_name" class="form-control" autocomplete="off" required>
          </div>

          <label for="item_name">ItemID</label>
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="item_name" id="item_name" class="form-control" autocomplete="off" required>
              <span class="input-group-btn">
                <button class="btn btn-info" type="button" id="select_item"><span class="glyphicon glyphicon-search"></span> เลือก Item</button>
              </span>
            </div>
          </div>

          <div class="form-group">
            <label for="gt_name">GreenTireID</label>
            <div class="input-group">
              <input type="text" name="gt_name" id="gt_name" class="form-control" autocomplete="off" required>
              <span class="input-group-btn">
                <button class="btn btn-info" type="button" id="select_greentire"><span class="glyphicon glyphicon-search"></span> เลือก Greentire</button>
              </span>
            </div><!-- /input-group -->
          </div>

          <input type="hidden" name="form_type">
          <input type="hidden" name="curetire_id">

          <label>
            <button class="btn btn-primary"><span class="glyphicon glyphicon-floppy-saved"></span> Save</button>
          </label>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- dialog item -->
<div class="modal" id="modal_item_create" tabindex="-1" role="dialog">
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
            <div id="grid_item"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>

<!-- dialog greentire -->
<div class="modal" id="modal_gt_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">GreenTire</h4>
      </div>
      <div class="modal-body">
        <form id="form_create_gt">
          <div class="form-group">
            <div id="grid_greentire"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  jQuery(document).ready(function($) {

    grid_cure();

    $('#select_item').on('click', function() {
      $('#modal_item_create').modal({
        backdrop: 'static'
      });
      grid_item();
    });

    $('#select_greentire').on('click', function() {
      $('#modal_gt_create').modal({
        backdrop: 'static'
      });
      grid_greentire();
    });

    $('#edit').on('click', function(e) {
      var rowdata = row_selected("#grid_cure");
      if (rowdata !== 'undefined') {
        $('#modal_curetire_create').modal({
          backdrop: 'static'
        });
        $('input[name=form_type]').val('update');
        $('.modal-title').text('Update');
        $('input[name=id_name]').prop('readonly', true);
        $('input[name=id_name]').val(rowdata.ID);
        $('input[name=des_name]').val(rowdata.Name);
        $('input[name=item_name]').val(rowdata.ItemID);
        $('input[name=gt_name]').val(rowdata.GreentireID);
      }
    });

    $('#print').on('click', function() {
      var rowdata = row_selected('#grid_cure');

      if (typeof rowdata !== 'undefined') {

        var printCode = rowdata.ID; // final
        var user_warehouse;

        gojax('get', base_url + '/api/user/warehouse')
          .done(function(data) {
            user_warehouse = data.warehouse;

            if (user_warehouse === 2) { // final
              printCode = rowdata.ID;
            } else if (user_warehouse === 3) { // fg
              printCode = rowdata.ItemID;
            } else {
              printCode = rowdata.ID;
            }

            window.open(base_url + '/generator/curetire/a5/' + printCode + '/' + user_warehouse, '_blank');
          });
      } else {
        alert('กรุณาเลือกข้อมูล');
      }
    });

    $("#form_create_curetire").on("submit", function(e) {
      e.preventDefault();

      var id_name = $('input[name=id_name]').val();
      if (!!id_name) {
        $.ajax({
            url: base_url + '/api/curetire/create',
            type: 'post',
            cache: false,
            dataType: 'json',
            data: $('form#form_create_curetire').serialize()
          })
          .done(function(data) {
            if (data.result != true) {
              //gotify(data.message, 'danger');
              alert(data.message);
            } else {
              $('#modal_curetire_create').modal('hide');
              $('#grid_cure').jqxGrid('updatebounddata');
            }
          });
      }
    });
  }); // end

  function modal_create_open() {
    $('#form_create_curetire').trigger('reset');
    $('.modal-title').text('Create new');
    $('input[name=id_name]').prop('readonly', false);
    $('input[name=form_type]').val('create');
  }

  function grid_cure() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: "json",
      datafields: [{
          name: "ID",
          type: "string"
        },
        {
          name: "Name",
          type: "string"
        },
        {
          name: "ItemID",
          type: "string"
        },
        {
          name: "GreentireID",
          type: "string"
        },
        {
          name: "Company",
          type: "string"
        },
        {
          name: "rate12",
          type: "number"
        },
        {
          name: "rate24",
          type: "number"
        },
        {
          name: "ItemQ",
          type: "string"
        }
      ],
      url: base_url + '/api/curetire/all'
    });

    return $("#grid_cure").jqxGrid({
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
      // theme: 'theme',
      columns: [{
          text: "ID",
          datafield: "ID",
          width: 100
        },
        {
          text: "Name",
          datafield: "Name",
          width: 200
        },
        {
          text: "ItemID",
          datafield: "ItemID",
          width: 100
        },
        {
          text: "Item Q",
          datafield: "ItemQ",
          width: 100
        },
        {
          text: "GreentireID",
          datafield: "GreentireID",
          width: 100
        },
        {
          text: "Company",
          datafield: "Company",
          width: 100
        },
        {
          text: "rate12",
          datafield: "rate12",
          width: 100
        },
        {
          text: "rate24",
          datafield: "rate24",
          width: 100
        }
      ]
    });
  }


  $('#grid_item').on('rowdoubleclick', function(event) {
    var args = event.args;
    var boundIndex = args.rowindex;
    var datarow = $("#grid_item").jqxGrid('getrowdata', boundIndex);
    $('#item_name').val(datarow.ID);
    // alert($('#item_name').val());
    $('#modal_item_create').modal('hide');
  });

  $('#grid_greentire').on('rowdoubleclick', function(event) {
    var args = event.args;
    var boundIndex = args.rowindex;
    var datarow = $("#grid_greentire").jqxGrid('getrowdata', boundIndex);
    $('#gt_name').val(datarow.ID);
    // alert($('#gt_name').val());
    $('#modal_gt_create').modal('hide');
  });


  function grid_item() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: "json",
      datafields: [{
          name: "ID",
          type: "string"
        },
        {
          name: "NameTH",
          type: "string"
        }
      ],
      url: base_url + '/api/item/all'
    });

    return $("#grid_item").jqxGrid({
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
      columns: [{
          text: "ID",
          datafield: "ID",
          width: 100
        },
        {
          text: "NameTH",
          datafield: "NameTH",
          width: 400
        }
      ]
    });

  }

  function grid_greentire() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: "json",
      datafields: [{
          name: "ID",
          type: "string"
        },
        {
          name: "Name",
          type: "string"
        }
      ],
      url: base_url + '/api/greentire/all'
    });

    return $("#grid_greentire").jqxGrid({
      width: '570',
      source: dataAdapter,
      pageable: true,
      autoHeight: true,
      filterable: true,
      showfilterrow: true,
      enableanimations: false,
      sortable: true,
      pagesize: 10,
      // theme: 'theme',
      columns: [{
          text: "ID",
          datafield: "ID"
        },
        {
          text: "Name",
          datafield: "Name"
        }
      ]
    });

  }
</script>