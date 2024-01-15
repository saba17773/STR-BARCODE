<?php $this->layout("layouts/base", ['title' => 'Greentire Inventory By Code Report']); ?>
<h1 class="head-text">Greentire Inventory By Code Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
    <div class="panel-body">
        <form id="form_curing" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/greentirecode" target="_blank">

            <div class="form-group">
                <label for="BOI">Greentire Code</label><br>
                <select name="selectMenuGT[]" id="selectMenuGT" multiple="multiple" style="width: 350px">
                </select>
            </div>


            <div class="form-group">
                <label for="BOI">BOI</label><br>
                <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" style="width: 350px">
                </select>
            </div>

            <!-- <div class="form-group">
                <label class="radio-inline" style="padding-left: 10px;">
                    <strong> Type : </strong>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="item_group" value="tbr" checked> <strong>TBR</strong>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="item_group" value="pcr"> <strong>PCR</strong>
                </label>
            </div> -->

            <button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#date_curing").datepicker({
            dateFormat: 'dd-mm-yy'
        });



        $('#selectMenuBOI').html("");
        $('#selectMenuBOI').multipleSelect({
            single: true
        });
        $('#selectMenuGT').html("");
        $('#selectMenuGT').multipleSelect({
            single: true,
            filter: true
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

        getPressSideGT()
            .done(function(data) {
                // $('#selectMenuGT').append('<option value="1">ALL</option>');
                $.each(data, function(k, v) {
                    $('#selectMenuGT').append('<option value="' + v.ID + '">' + v.Name + '</option>');
                });
                $('#selectMenuGT').multipleSelect({
                    single: true
                });
            });


    });



    function getPressSideBOI() {
        return $.ajax({
            url: base_url + '/api/press/allBOI',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }

    function getPressSideGT() {
        return $.ajax({
            url: base_url + '/api/press/allGT',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }
</script>