<?php $this->layout("layouts/base", ["title" => "Cure Tire Code Master Report"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Cure Tire Code Master Report</div>
	<div class="panel-body">
		<div class="btn-group btn-group-justified" role="group">
			<div class="btn-group">
				<button type="button" class="btn btn-primary btn-lg" id="to_pdf"><span class="glyphicon glyphicon-print"></span> Print to PDF</button>
			</div>
		  <div class="btn-group">
		  	<button type="button" class="btn btn-success btn-lg" id="to_excel"><span class="glyphicon glyphicon-file
"></span> Export to Excel</button>
		  </div>
		</div>
	</div>
</div>


<script>
	jQuery(document).ready(function($) {
		$('#to_pdf').on('click', function(event) {
			event.preventDefault();
			/* Act on the event */
			window.open(base_url+'/report/curetire/master/pdf', '_blank');
		});		

		$('#to_excel').on('click', function(event) {
			event.preventDefault();
			/* Act on the event */
			window.open(base_url+'/report/curetire/master/excel', '_blank');
		});		
	});
</script>