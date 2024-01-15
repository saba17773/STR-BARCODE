<?php $this->layout("layouts/base", ['title' => 'Scrap Checking']); ?>

<h1 class="head-text">Scrap Checking</h1>

<div class="panel panel-default form-center">
  <div class="panel-body">
    <form id="formScrapCheck">
	    <div class="form-group">
	    	<label for="barcode">Barcode</label>
	        <input type="text" class="form-control input-lg" id="barcode" 
                name="barcode" autocomplete="off" autofocus required />
	    </div>
        <!-- <span id="result" style="font-weight: bold;"></span> -->
	</form>
  </div>
</div>



<script type="text/javascript">

    jQuery(document).ready(function($) 
    {
        $('#barcode').keydown(function(event) 
        {
            
			if (event.which === 13) 
            {
                event.preventDefault();

                barcode = $('#barcode').val();
                
                gojax_f('post', base_url+'/scrapchk', '#formScrapCheck')
				.done(function(chk)
                { 
					if(chk.status == 200)
                    { 
                        // document.getElementById("result").style.color = "green";
                        // $('#result').text(chk.message);
                        $('#top_alert').show();
						$('#top_alert_message').text(chk.message);
						$('#modal_alert').modal('hide');

					} 
                    else 
                    {
                        // document.getElementById("result").style.color = "green";
						// $('#result').text('');

                        $('#top_alert').hide();
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(chk.message);

                        
					}
				});

                $('#barcode').val('').focus();

			}
		});			
	});
    

</script>