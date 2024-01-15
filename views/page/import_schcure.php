<?php $this->layout("layouts/base", ['title' => 'Import Schedule Cure']); ?>
<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 700px; margin: auto;">
    <div class="panel-heading">Import Schedule Cure</div>
    <div class="panel-body">

        <?php if (isset($_GET["r"]) && $_GET["r"] === "success") { ?>
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><strong>อัพโหลดไฟล์สำเร็จ :)</strong></p>
                <p>ดำเนินการเสร็จสิ้น <?php echo $_GET["total"]; ?> รายการ</p>
                <p>ข้อมูลที่ไม่สามารถ Import ได้ <?php echo $_GET["total"] - $_GET["import"]; ?> รายการ</p>
            </div>
        <?php } ?>

        <?php if (isset($_GET["r"]) && $_GET["r"] === "failed") { ?>
            <div class="alert alert-danger" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><strong>อัพโหลดไฟล์ล้มเหลว :(</strong></p>
                <!-- <p>ดำเนินการเสร็จสิ้น <?php echo $_GET["total"]; ?> รายการ</p> -->
                <!-- <p>ข้อมูลที่ไม่สามารถ Import ได้ <?php echo $_GET["total"] - $_GET["import"]; ?> รายการ</p> -->
                <p>ทำการตรวจสอบ Press No หรือ Press Side หรือ CureCode อีกครั้ง </p>
            </div>
        <?php } ?>

        <form action="<?php echo APP_ROOT; ?>/api/import/schcure" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="import_schcure" required>
            </div>

            <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-import"></span> Import</button>
        </form>
    </div>
</div>

<div class="head-space"></div>

<div class="panel panel-info" style="max-width : 700px; margin: auto;">
    <div class="panel-heading">ตัวอย่างข้อมูลสำหรับ Import ไฟล์ Excel (.xlsx) หรือ LibreOffice (.ods) </div>
    <div class="panel-body">
        <img src="/resources/example/schedule_cure.png">
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('form').on('submit', function(event) {
            if (!confirm('คุณต้องการ Import Schedule Cure ใช่หรือไม่?')) {
                event.preventDefault();
            }
        });
    });
</script>