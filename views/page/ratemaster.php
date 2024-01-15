<?php $this->layout("layouts/base", ['title' => 'Rate Master']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<style>
	td {
		padding: 5px;
	}
</style>
<div class="head-space"></div>
<div class="panel panel-default" id="panel_manage">

	<div class="panel-heading">
 		กำหนดเงื่อนไขการคำนวณค่าเรท
	</div>

    <div class="panel-body">
		<div class="panel-group">
            <div class="form-group">
                <input type="button" name="build" id="build" class="btn btn-primary btn-sm" value="Build" style="width:80px">
                <input type="button" name="cure" id="cure" class="btn btn-primary btn-sm" value="Cure" style="width:80px">
            </div>
        </div>
		
		<h4><span id="grouptxt" ></span><span id="subgrouptxt" ></span></h4>
		<div class="panel-group">
            <div class="form-group">
				<button class="btn btn-success" id="btn_create"> สร้างเงื่อนไข </button>
                <button class="btn btn-info" id="btn_link"> ผูกการเชื่อมโยง </button>
				<button class="btn btn-warning" id="btn_edit"> แก้ไข </button>
            </div>
        </div>
        
    </div>

    <div class="modal" id="modal_create_build" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width:800px;margin:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">สร้างเงื่อนไขใหม่</h4>
                </div>
                
                <div class="modal-body">	
                    <form id="form_create_build">
                        <div class="form-group">
                            <!-- <input type="button" name="building" id="c_building" class="btn btn-primary btn-sm" value="Building" style="width:120px">
                            <input type="button" name="changecode" id="c_changecode" class="btn btn-primary btn-sm" value="Change Code" style="width:120px"> -->
                            <input type="button" name="c_edit" id="c_new" class="btn btn-primary btn-sm" value="Create" style="width:120px">
                            <input type="button" name="c_edit" id="c_edit" class="btn btn-warning" value="Edit" style="width:120px">
                        </div>

                        <div class="panel-group" id="panel_newData">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td>
                                            เงื่อนไขที่ 1 :
                                        </td>
                                        <td>
                                            <input type="text" name="b_Qty1" id="b_Qty1" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            ประเภทการจ่าย :
                                        </td>
                                        <td>
                                            <select name="payment1[]" id="payment1"  multiple="multiple" 
                                            style="width: 120px" required>
                                        </td>
                                        <td>
                                            <input type="text" name="b_Price1" id="b_Price1" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            <span id="b_text1" ></span>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td>
                                            เงื่อนไขที่ 2 :
                                        </td>
                                        <td>
                                            <input type="text" name="b_Qty2" id="b_Qty2" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            ประเภทการจ่าย :
                                        </td>
                                        <td>
                                            <select name="payment2[]" id="payment2"  multiple="multiple" 
                                            style="width: 120px" required>
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="b_Price2" id="b_Price2" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            <span id="b_text2" ></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            เงื่อนไขที่ 3 :
                                        </td>
                                        <td>
                                            <input type="text" name="b_Qty3" id="b_Qty3" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            ประเภทการจ่าย :
                                        </td>
                                        <td>
                                            <select name="payment3[]" id="payment3"  multiple="multiple" 
                                            style="width: 120px" required>
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="b_Price3" id="b_Price3" autocomplete="off" class="form-control" style="width: 100px">
                                        </td>
                                        <td>
                                            <span id="b_text3" ></span>
                                        </td>
                                    </tr>
                                </table>

                                <table>
                                    <tr>
                                        <td style="width:85%">
                                        </td>
                                        <td>
                                            <button type="submit" id="btnSaveCBuild" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-download"></span>
                                                บันทึกข้อมูล
                                            </button> 
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div id="grid_seq"></div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="modal_edit_build" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="width:800px;margin:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">แก้ไขเงื่อนไข</h4>
                </div>
                
                <div class="modal-body">	
                    <form id="form_edit_build">
                        <div class="form-group">
                            <table>
                                <tr>
                                    <td>
                                        เงื่อนไข :
                                    </td>
                                    <td>
                                        <input type="text" name="E_Qty1" id="E_Qty1" autocomplete="off" class="form-control" style="width: 100px">
                                    </td>
                                    <td>
                                        ประเภทการจ่าย :
                                    </td>
                                    <td>
                                        <select name="E_payment1[]" id="E_payment1"  multiple="multiple" 
                                        style="width: 120px" required>
                                    </td>
                                    <td>
                                        <input type="text" name="E_Price1" id="E_Price1" autocomplete="off" class="form-control" style="width: 100px">
                                    </td>
                                    <td>
                                        <span id="E_text1" ></span>
                                    </td>
                                            
                                </tr>
                            </table>

                            <table>
                                <tr>
                                    <td style="width:85%">
                                    </td>
                                    <td>
                                        <button type="submit" id="btnSaveEBuild" class="btn btn-primary">
                                            <span class="glyphicon glyphicon-download"></span>
                                            บันทึกข้อมูล
                                        </button> 
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>



<script type="text/javascript">

    jQuery(document).ready(function($) 
	{
        $('#btn_create').hide();
		$('#btn_link').hide();
        $('#btn_edit').hide();
        
        // $('#build').prop('disabled',true);
		// $('#cure').prop('disabled',false);

        // $('#btn_create').prop('disabled',true);
		// $('#btn_link').prop('disabled',false);
        // $('#btn_edit').prop('disabled',false);
        $('#build').on('click', function()
        {
            $('#build').prop('disabled',true);
			$('#cure').prop('disabled',false);
			$('#btn_create').show();
			$('#btn_link').show();
            $('#btn_edit').show();

        });

        $('#cure').on('click', function()
        {
            $('#build').prop('disabled',false);
			$('#cure').prop('disabled',true);
			$('#btn_create').show();
			$('#btn_link').show();
            $('#btn_edit').show();
        });

        $('#btn_create').on('click',function()
        {
            bindGridSEQ();

            event.preventDefault();
            $('#modal_create_build').modal({backdrop: 'static'});
                
            $('#panel_newData').hide();

            $("#form_create_build")[0].reset();
            
            
        });

        $('#btn_link').on('click',function()
        {
            if($('#build').prop('disabled')===true)
            {
                alert("LinK Build !!");
            }
            else
            {
                alert("LinK Cure !!");
            }
            // event.preventDefault();
            // $('#modal_create_build').modal({backdrop: 'static'});
			// $("#form_add_cure")[0].reset();

        });

        $('#btn_edit').on('click',function()
        {
            if($('#build').prop('disabled')===true)
            {
                alert("Edit Build !!");
            }
            else
            {
                alert("Edit Cure !!");
            }
            // event.preventDefault();
            // $('#modal_create_build').modal({backdrop: 'static'});
			// $("#form_add_cure")[0].reset();

        });

        $('#c_new').on('click',function()
        {
            $('#payment1').html("");
            $('#payment2').html("");
            $('#payment3').html("");

            $('#panel_newData').show();

            getPaymentMaster()
            .done(function(data) {
                $.each(data, function(k, v) {
                    $('#payment1').append('<option value="'+ v.ID +'" >'+v.Description+'</option>');
                });
                $('#payment1').multipleSelect({single: true});
            });

            getPaymentMaster()
            .done(function(data) {
                $.each(data, function(k, v) {
                    $('#payment2').append('<option value="'+ v.ID +'" >'+v.Description+'</option>');
                });
                $('#payment2').multipleSelect({single: true});
            });

            getPaymentMaster()
            .done(function(data) {
                $.each(data, function(k, v) {
                    $('#payment3').append('<option value="'+ v.ID +'" >'+v.Description+'</option>');
                });
                $('#payment3').multipleSelect({single: true});
            });

        });

        $('#c_edit').on('click',function()
        {
            event.preventDefault();
            var rowdata = row_selected('#grid_seq');
            
			if (typeof rowdata !== 'undefined') 
			{
                $('#panel_newData').hide();
                $('#E_payment1').html("");

				$('#modal_edit_build').modal({backdrop: 'static'});
                
                

				ID = rowdata.ID;
                SeqID = rowdata.SeqID;
                QtyMin = rowdata.QtyMin;
                QtyMax = rowdata.QtyMax;
                Price = rowdata.Price;
                Formula = rowdata.Formula;
                Payment = rowdata.Payment;

                $('#E_Qty1').val(QtyMin);
                $('#E_Price1').val(Formula);
                // $('#E_payment1').val() = Payment;
                getPaymentMaster()
                .done(function(data) {
                    $.each(data, function(k, v) {
                        $('#E_payment1').append('<option value="'+ v.ID +'" >'+v.Description+'</option>');
                    });
                    $('#E_payment1').val(Payment);
                    $('#E_payment1').multipleSelect({single: true});
                    
                });

                // $('#E_payment1').html(Payment);
                console.log(Payment);
                // document.getElementById("E_payment1").value = 1;

                if(Payment === 1)
                {
                    $('#E_text1').text("บาท");
        
                    
                }
                else if(Payment === 2)
                {
                    $('#E_text1').text("บาท");
                    
                    
                }
                else if(Payment === 3)
                {
                    $('#E_text1').text("เส้น/บาท");
                    
                }
                else
                {
                    $('#E_text1').text("");
                }
				

                // $('#E_payment1').text("1");
               
                
                $('#panel_newData').hide();
                $('#panel_EditData').show();
				
			} 
			else 
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
			}
            

        });

        $('#form_edit_build').on('submit', function(event) 
		{
			$('#modal_edit_build').modal('hide');
            event.preventDefault();
            
            alert($('#E_payment1').val());

		});
        // $('#c_changecode').on('click',function()
        // {
        //     $('#c_building').prop('disabled',false);
		// 	$('#c_changecode').prop('disabled',true); 

        //     $('#pc_build').hide();
        //     $('#pc_change').show();
        // });

        $('#payment1').on('change', function()
		{
            if(this.value === '1' || this.value === '2')
            {
                $('#b_text1').text("บาท");
            }
            else if(this.value === '3')
            {
                $('#b_text1').text("เส้นบาท");
            }
            else
            {
                $('#b_text1').text("");
            }
           
        });

        $('#payment2').on('change', function()
		{
            if(this.value === '1' || this.value === '2')
            {
                $('#b_text2').text("บาท");
            }
            else if(this.value === '3')
            {
                $('#b_text2').text("เส้นบาท");
            }
            else
            {
                $('#b_text2').text("");
            }
           
        });

        $('#payment3').on('change', function()
		{
            if(this.value === '1' || this.value === '2')
            {
                $('#b_text3').text("บาท");
            }
            else if(this.value === '3')
            {
                $('#b_text3').text("เส้นบาท");
            }
            else
            {
                $('#b_text3').text("");
            }
           
        });

        $('#form_create_build').on('submit', function(event) 
		{
			$('#modal_create_build').modal('hide');
			event.preventDefault();

			b_Qty1 = $('#b_Qty1').val();
			b_Qty2 = $('#b_Qty2').val();
			b_Qty3 = $('#b_Qty3').val();
			payment1 = $('#payment1').val();
			payment2 = $('#payment2').val();
			payment3 = $('#payment3').val();
			b_Price1 = $('#b_Price1').val();
            b_Price2 = $('#b_Price2').val();
            b_Price3 = $('#b_Price3').val();

            alert(b_Qty1 + " " + b_Qty2 + " " +b_Qty3);

		});







        function getPaymentMaster() 
		{
			return $.ajax({
				url : base_url + '/api/ratemaster/getPayment',
				type : 'get',
				dataType : 'json',
				cache : false
			});
		}

        function bindGridSEQ() 
		{
			
			var dataAdapter = new $.jqx.dataAdapter({
				datatype: 'json',
				datafields: [
				{ name: 'ID', type: 'int'},
				{ name: 'SeqGrpID', type: 'int'},
				{ name: 'SeqID', type: 'int'},
				{ name: 'QtyMin', type: 'int' },
				{ name: 'QtyMax', type: 'int'},
				{ name: 'Price', type: 'int' },
				{ name: 'Formula', type: 'int'},
                { name: 'Payment', type: 'int'},
				{ name: 'Remark', type: 'string'}
				
				],
				url: base_url + "/api/ratemaster/bindGrid_SEQ"
			});

			return $("#grid_seq").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				pageSize : 10,
				altrows : true,
				pageable : true,
				sortable: true,
				filterable : true,
				showfilterrow : true,
				columnsresize: true,
				columns: [
				{ text: 'รหัสกลุ่ม', datafield: 'SeqGrpID', width: 100},
				{ text: 'ลำดับการคิดเงิน', datafield: 'SeqID', width: 100},
				{ text: 'จำนวนต่ำสุด', datafield: 'QtyMin', width: 100},
				{ text: 'จำนวนสูงสุด', datafield: 'QtyMax', width: 80},
				{ text: 'จำนวนเงินที่จ่าย', datafield: 'Price', width: 100},
				{ text: 'สูตรคำนวณ', datafield: 'Formula', width: 80},
				{ text: 'ประเภทการคิดเงิน', datafield: 'Payment', width: 80}, 
				{ text: 'หมายเหตุ', datafield: 'Remark', width: 250}
				]
			});
        }
        
        // function getPaymentMaster_param(payment) 
		// {
		// 	return $.ajax({
		// 		url : base_url + '/api/ratemaster/getPaymentparam/'+payment,
		// 		type : 'get',
		// 		dataType : 'json',
		// 		cache : false
		// 	});
		// }


    });

</script>