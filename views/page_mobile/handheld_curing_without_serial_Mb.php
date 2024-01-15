<?php

// $this->layout("layouts/handheld", ['title' => 'Handheld Curing']);
$this->layout("layouts/handheldmobile", ['title' => 'Handheld Curing']); ?>


<!-- <hr>
<div style="padding: 0; width: 100%; text-align: center;" >
  <a class="btn btn-primary btn-block btn-sm" href="<?php echo APP_ROOT; ?>/curing">
  	<span class="glyphicon glyphicon-home"></span> Home
  </a>
  |
  <a class="btn btn-danger btn-block btn-sm" href="<?php echo APP_ROOT; ?>/user/logout">
    <span class="glyphicon glyphicon-log-out"></span> Logout
  </a>
</div>
<hr> -->
<div class="panel panel-default form-center" id="panel_auth">
    <div class="panel-heading"><h1>Curing PCR/AP</h1></div>
        <div class="panel-body">
            <form id="form_curing">

                <div class="form-group">
                    <label for="">Curing Code</label><br>
                    <input type="text" id="curing_code" name="curing_code" >
                </div>

                <div class="form-group">
                    <label for="">Barcode</label><br>
                    <input type="text" id="barcode" name="barcode" >
                </div>
                
                <input type="hidden" name="cure_type" value="without_serial" />
            </form>
        </div>
    </div>
</div>

<div id="show_result"></div>

<script>
    
    jQuery(document).ready(function($) {

        $('input[name=curing_code]').keydown(function(event) {
            if (event.which === 13) {
                $("#barcode").focus();
            }
        });
        $('input[name=barcode]').keydown(function(event) {
            if (event.which === 13) {
                $('form#form_curing').submit();
            }
        });

        $('#show-error').on('click', function() {
            $('#show-error').hide();
            $('#top_alert').hide();

            $('#form_curing').trigger('reset');
            $('#curing_code').focus();
        });

        $('form#form_curing').on('submit', function(e) {
            e.preventDefault();

            // $('#curing_code').css({
            //     'background': '#eeeeee'
            // }).prop('readonly', true);
            // // $('#template_code').css({'background': '#eeeeee'}).prop('readonly', true);
            // $('#barcode').css({
            //     'background': '#eeeeee'
            // }).prop('readonly', true);
            gojax_f('post', '/api/curing/save', '#form_curing')
                .done(function(data) {
                    var temp_barcode = data.message;
                    if (data.status == 200) {          
                        $('#show-ok').show();
						$('#show-ok-text1').text(data.pressno);
						$('#show-ok-text2').text(data.pressside);
						$('#show-ok-text3').text(data.curecode);
						$('#show-ok-text4').text(data.batch);
						$('#show-ok').on('click', function() {
							$('#show-ok').hide();

							$('#modal_alert_pass_curring').show();
							setTimeout(function() {
								$('#modal_alert_pass_curring').hide();
							}, 2000);

							$('#top_alert').show();
							$('#top_alert_message').text(data.curecode + ', ' + data.batch + ' => ' + temp_barcode);
							$('#modal_alert').modal('hide');
						});

						// $('#modal_alert_pass_curring').show();
						// setTimeout(function () 
						// {
						// 	$('#modal_alert_pass_curring').hide();
						// }, 2000);

						// $('#top_alert').show();
						// $('#top_alert_message').text(data.curecode + ', '+ data.batch +' => ' + temp_barcode);
						// $('#modal_alert').modal('hide');

					} else {
                        
						$('#show-error').show();
						$('#show-error-text').text(data.message);
                        header("Refresh:0");

					}
                    
					$('#curing_code').css({
						'background': '#ffffff'
					}).prop('readonly', false);
					$('#template_code').css({
						'background': '#ffffff'
					}).prop('readonly', false);
					$('#barcode').css({
						'background': '#ffffff'
					}).prop('readonly', false);
					$('form#form_curing').trigger('reset');
					document.getElementById("curing_code").focus();
				})
				.fail(function() {
					$('#curing_code').css({
						'background': '#ffffff'
					}).prop('readonly', false);
					$('#template_code').css({
						'background': '#ffffff'
					}).prop('readonly', false);
					$('#barcode').css({
						'background': '#ffffff'
					}).prop('readonly', false);

					$('#show_result').css({
							'margin': '10px auto',
							'text-align': 'center',
							'padding': '0px 5px',
							'color': 'red'
						})
						.text('cannot send data to server.');
				});
		});

		$("#curing_code").focus();


    });

    function setFocus() {
        document.getElementById('curing_code').focus();
        return false;
    }
    // function form_curing_submit() {
    // 	gojax_f('post', base_url+'/api/curing/save' ,'#form_curing')
    // 		.done(function(data) {
    // 			if (data.status == 200) {
    // 				$('#show_result')
    // 					.css({
    // 						'margin': '10px 0px',
    // 						'padding': '0px 5px',
    // 						'color': 'green'
    // 					})
    // 					.text(data.message);
    // 					// Hide Success
    // 					setTimeout(function() {
    // 						$('#show_result').css('margin', '10px 0px').text('');
    // 					}, 3000);
    // 			} else {
    // 				$('#show_result')
    // 					.css({
    // 						'margin': '10px 0px',
    // 						'padding': '0px 5px',
    // 						'color': 'red'
    // 					})
    // 					.text(data.message);

    // 				$('#show-error').show();
    // 				$('#show-error-text').text(data.message);
    // 			}

    // 			$('form#form_curing').trigger('reset');
    // 			document.getElementById("curing_code").focus();
    // 		})
    // 		.fail(function() {

    // 			$('#show_result')
    // 				.css({
    // 					'margin': '10px 0px',
    // 					'padding': '0px 5px',
    // 					'color': 'red'
    // 				})
    // 				.text("ทำรายการไม่สำเร็จ");

    // 			$('form#form_curing').trigger('reset');
    // 			document.getElementById("curing_code").focus();
    // 		});

    // 	return false;
    // }
</script>


<!-- JS -->
<script>
    var base_url = '';
</script>
<script src="/assets/js/jquery-1.12.0.min.js"></script>
<script src="/assets/js/fastclick.js"></script>
<script src="/assets/js/gojax.min.js"></script>
<script src="/assets/js/app.js"></script>

<!--[if lt IE 9]>
		<script src="/assets/js/html5shiv.js"></script>
		<script src="/assets/js/respond.js"></script>
	<![endif]-->
</head>

<body>

    <div id="show-error">
        <table border="0" width="100%">
            <tr>
                <td valign="top" align="center">
                    <br><br>
                    <img data-dismiss="modal" width="70" height="70" src="/assets/images/error01.png" alt="">
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-error-text" style="color: white;"></b>
                </td>
            </tr>
        </table>
    </div>
    <div id="show-ok">
        <table border="0" width="100%">
            <tr>
                <td valign="top" align="center">
                    <br>
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-ok-text1" style="color: white;"></b>
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-ok-text2" style="color: white;"></b>
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-ok-text3" style="color: white;"></b>
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <b id="show-ok-text4" style="color: white;"></b>
                </td>
            </tr>

        </table>
    </div>
    <?php echo $this->section("content"); ?>
    <script>
        function close_window() {
            window.open('', '_self', '');
            window.close();
        }
    </script>