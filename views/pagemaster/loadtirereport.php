<?php $this->layout("layouts/base", ['title' => 'ใบรายงาน จำนวนการโหลดยางขึ้นตู้']); ?>
<style>
    .btn-xl {
        padding: 5px 40px;
        font-size: 20px;
        border-radius: 5px;
    }
</style>
<div class="head-space"></div>
<div class="panel panel-default form-center">
    <div class="panel-heading">ใบรายงาน จำนวนการโหลดยางขึ้นตู้</div>
    <div class="panel-body">
        <form id="form_building" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/loadtireount" target="_blank">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" id="dateloadtire" name="dateloadtire" class=form-control autocomplete="off" required placeholder="เลือกวันที่..." />
                <input type="text" id="check_type" name="check_type" hidden />
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <br>
                <select name="selectbrand[]" id="selectbrand" multiple="multiple" style="width: 400px">
                </select>
            </div>

            <div class="form-group">
                <label for="BOI">BOI</label><br>
                <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" style="width: 400px">
                </select>
            </div>
            <div class="form-group">
                <label class="radio-inline" style="padding-left: 10px;">
                    <strong> Type : </strong>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="item_group" value="tbr" checked> <strong>TBR</strong>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="item_group" value="pcr"> <strong>PCR</strong>
                </label>
            </div>

            <input type="hidden" name="warehouse" id="warehouse" value="recive">
            <button type="button" class="btn btn-primary btn-xl " id="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
            <button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
        </form>
    </div>
</div>
<script type="text/javascript">
    $("#dateloadtire").datepicker({
        dateFormat: 'dd-mm-yy'
    });
    $('#selectMenuBOI').html("");
    $('#selectMenuBOI').multipleSelect({
        single: true
    });


    getBrand()
        .done(function(data) {
            $.each(data, function(k, v) {
                $('#selectbrand').append('<option value="' + v.BrandID + '">' + v.BrandName + '</option>');
            });
            $('#selectbrand').multipleSelect();
        });
    getPressSideBOI()
        .done(function(data) {
            $('#selectMenuBOI').append('<option value="1">ALL</option>');
            $.each(data, function(k, v) {
                $('#selectMenuBOI').append('<option value="' + v.ID + '">' + v.ID + '</option>');
            });
            $('#selectMenuBOI').multipleSelect({
                single: true
            });
        });

    $('#to_pdf').on('click', function(event) {
        event.preventDefault();
        $('#to_excel').attr("disabled", true);
        setTimeout(function() {
            $('#to_excel').attr("disabled", false);
        }, 10000);
        $('input[name=check_type]').val(1);
        $('#form_building').submit();

    });

    $('#to_excel').on('click', function(event) {
        event.preventDefault();
        $('#to_pdf').attr("disabled", true);
        setTimeout(function() {
            $('#to_pdf').attr("disabled", false);
        }, 10000);
        $('input[name=check_type]').val(2);
        $('#form_building').submit();
    });



    function getBrand() {
        return $.ajax({
            url: base_url + '/api/brand/allbrand',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }

    function getPressSideBOI() {
        return $.ajax({
            url: base_url + '/api/press/allBOI',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }
</script>