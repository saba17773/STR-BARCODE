<?php $this->layout("layouts/base", ['title' => 'Monthly Rate Build Report']); ?>
<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>
<h1 class="head-text">รายงานการจ่ายเงินค่าเลท รายเดือน</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_rate" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/rate_month"  target="_blank">
		<div class="form-group">
			<label for="date">Month</label>
			<input type="text" id="month" name="month" class=form-control required  placeholder="เลือกเดือน" autocomplete="off" />
		</div>

        <div class="form-group" style="display: block;">
			<strong>Type : </strong>
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="tbr" checked/> TBR
			</label>
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="pcr" /> PCR
			</label>
		</div>
        <input type="text" id="check_type" name="check_type" hidden/>
		<!-- <button type="submit" id ="view" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button> -->
        <!-- <button type="submit" id ="download" class="glyphicon glyphicon-save"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Download Report</button> -->
        <button type="button" class="btn btn-primary btn-xl " style="width: 180px; margin: 0 auto;" id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
    	<button type="button" class="btn btn-success btn-xl " style="width: 180px; margin: 0 auto;" id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>
	</form>
  </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) 
    {
   
        $('#month').datepicker( {changeMonth: true,changeYear: true,
                                showButtonPanel: true,dateFormat: 'mm-yy',
                                onClose: function(dateText, inst) 
            { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
        
        $('#to_pdf').on('click', function(event) 
        {   
            month_input = $('#month').val();

            if(month_input !== "" )
            {
                event.preventDefault();
                $('input[name=check_type]').val(1);
                $('#form_rate').submit();
            }
            else 
            {
                alert("กรุณาเลือกเดือนก่อน");
                exit();

            }
	        

        });

        $('#to_excel').on('click', function(event) 
        {
            month_input = $('#month').val();
 
            if(month_input !== "" )
            {
                event.preventDefault();
                $('input[name=check_type]').val(2);
                $('#form_rate').submit();
            }
            else 
            {
                alert("กรุณาเลือกเดือนก่อน");
                exit();

            }

        });


    });

</script>