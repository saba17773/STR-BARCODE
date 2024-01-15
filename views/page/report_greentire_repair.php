<?php $this->layout("layouts/base", ["title" => "Report Greentire Repair"]) ?>
<style>
    .btn-xl {
        padding: 5px 45px;
        font-size: 20px;
        border-radius: 5px;
    }
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
    <div class="panel-heading">Greentire Repair Report</div>
    <div class="panel-body">
        <form id="formDateScrap" method="post" action="/report/greentire/greentireRepairPdf" target="_blank">
            <div class="form-group">
                <label for="date_scrap">Date Scrap</label>
                <input type="date" name="date_scrap" id="date_scrap" class="form-control">
                <input type="text" id="check_type" name="check_type" hidden />
            </div>

            <div class="form-group">
                <label for="BOI">BOI</label><br>
                <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple" style="width: 470px" required>
                </select>

            </div>

            <div class="form-group" style="display: block;">
                <strong>Type : </strong>
                <label style="padding-left: 40px;">
                    <input type="radio" name="item_group" id="item_group" value="tbr" /> TBR
                </label>
                <label style="padding-left: 40px;">
                    <input type="radio" name="item_group" id="item_group" value="pcr" /> PCR
                </label>
            </div>

            <button type="button" class="btn btn-primary btn-xl " id="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
            <button type="button" class="btn btn-success btn-xl " id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#date_scrap').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('#selectMenuBOI').html("");
        $('#selectMenuBOI').multipleSelect({
            single: true
        });



        // $('#to_pdf').on('click', function(event) {
        // 	event.preventDefault();
        // 	var date_scrap = $('#date_scrap').val();
        // 	var product_group = $('input[name=item_group]:checked').val();
        // 	var check =1;
        // 	$('#to_excel').attr("disabled", true);
        // 	setTimeout(function () {
        // 	$('#to_excel').attr("disabled", false);
        // 	}, 10000);
        //   if (!!date_scrap) {
        //     $('#formDateScrap').submit(	window.open(base_url+'/report/greentire/scrap/'+date_scrap+'/'+product_group+'/'+check, '_blank'));
        //   }
        // });

        // $('#to_excel').on('click', function(event) {
        // 		event.preventDefault();
        // 		var date_scrap = $('#date_scrap').val();
        // 		var product_group = $('input[name=item_group]:checked').val();
        // 			var check =2;
        // 			$('#to_pdf').attr("disabled", true);
        // 			setTimeout(function () {
        //         $('#to_pdf').attr("disabled", false);
        // 			}, 10000);
        // 			if (!!date_scrap) {
        //         $('#formDateScrap').submit(	window.open(base_url+'/report/greentire/scrap/'+date_scrap+'/'+product_group+'/'+check, '_blank'));
        //       }
        // });
        $('#to_pdf').on('click', function(event) {
            event.preventDefault();
            $('#to_excel').attr("disabled", true);
            setTimeout(function() {
                $('#to_excel').attr("disabled", false);
            }, 10000);
            $('input[name=check_type]').val(1);
            $('#formDateScrap').submit();

        });

        $('#to_excel').on('click', function(event) {
            event.preventDefault();
            $('#to_pdf').attr("disabled", true);
            setTimeout(function() {
                $('#to_pdf').attr("disabled", false);
            }, 10000);
            $('input[name=check_type]').val(2);
            $('#formDateScrap').submit();

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
    });

    function getPressSideBOI() {
        return $.ajax({
            url: base_url + '/api/press/allBOI',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }
</script>