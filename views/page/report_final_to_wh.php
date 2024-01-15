<?php $this->layout("layouts/base", ["title" => "Report Final Send To Warehouse"]) ?>
<style>
    .btn-xl {
        padding: 5px 45px;
        font-size: 20px;
        border-radius: 5px;
    }
</style>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
    <div class="panel-heading">Final Send To Warehouse</div>
    <div class="panel-body">
        <form id="formDateScrap" method="post" action="/report/finalsendwh/FisnlSendwhPdf" target="_blank">
            <div class="form-group">
                <label for="date_scrap">Date</label>
                <input type="date" name="date_scrap" id="date_scrap" class="form-control">
                <input type="text" id="check_type" name="check_type" hidden />
            </div>

            <div class="form-group">
                <label for="shift">Shift</label>
                <select name="shift" id="shift" class="form-control" required>
                    <option value="day">กลางวัน</option>
                    <option value="night">กลางคืน</option>
                </select>
            </div>

            <div class="form-group">
                <label for="time">Time</label><br>
                <select name="selectTruck[]" id="selectTruck" multiple="multiple" style="width: 465px;" required>
                </select>
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
        $('#selectTruck').html("");
        // $('#selectTruck').multipleSelect({
        //     single: true
        // });
        getPressSide()
            .done(function(data) {
                $.each(data, function(k, v) {
                    $('#selectTruck').append('<option value="' + v.PlateNumber + '">' + v.PlateNumber + '</option>');
                });
                $('#selectTruck').multipleSelect();
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


    });

    // function getPressSideBOI() {
    //     return $.ajax({
    //         url: base_url + '/api/press/alltruck',
    //         type: 'get',
    //         dataType: 'json',
    //         cache: false
    //     });
    // }

    function getPressSide() {
        return $.ajax({
            url: base_url + '/api/press/alltruck',
            type: 'get',
            dataType: 'json',
            cache: false
        });
    }
</script>