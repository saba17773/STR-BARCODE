<?php $this->layout("layouts/base", ['title' => 'Import top turn']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 700px; margin: auto;">
	<div class="panel-heading">Import Topturn</div>
	<div class="panel-body">
		
		<?php if (isset($_GET["r"]) && 
			isset($_GET["total"]) && 
			isset($_GET["import"]) &&
			isset($_GET["not_import"]) &&  
			$_GET["r"] === "success") { ?>
			<div class="alert alert-success" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<p><strong>อัพโหลดไฟล์สำเร็จ :)</strong></p>
				<p>ดำเนินการเสร็จสิ้น <?php echo $_GET["import"]; ?> รายการ</p>
				<p>ข้อมูลที่ไม่สามารถ Import ได้ <?php echo $_GET["not_import"]; ?> รายการ</p>
			</div>
		<?php } ?>

		<?php if (isset($_GET["r"]) && 
			isset($_GET["total"]) && 
			isset($_GET["import"]) &&
			isset($_GET["not_import"]) && 
			$_GET["r"] === "failed") { ?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<p><strong>อัพโหลดไฟล์ล้มเหลว :(</strong></p>
				<p>ข้อมูลที่ไม่สามารถ Import ได้ <?php echo $_GET["total"] - $_GET["import"]; ?> รายการ</p>
			</div>
		<?php } ?>

		<form action="<?php echo APP_ROOT; ?>/api/import/topturn" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<input type="file" name="import_topturn" required>
			</div>
			
			<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-import"></span> Import</button>
		</form>
	</div>
</div>

<div class="head-space"></div>

<div class="panel panel-info" style="max-width: 700px; margin: auto;">
	<div class="panel-heading">ตัวอย่างข้อมูลสำหรับ Import ไฟล์ Excel (.xlsx)</div>
	<div class="panel-body">
		<img src="/resources/example/top_turn.png">
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('form').on('submit', function(event) {
			if (!confirm('คุณต้องการ Import Top Turn ใช่หรือไม่?')) {
				event.preventDefault();
			}
		});
	});
</script>