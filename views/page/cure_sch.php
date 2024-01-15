<?php $this->layout("layouts/base", ['title' => 'Cure import Schedule']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
    <div class="panel-body">
        <form id="form_rate" method="post" action="<?php echo APP_ROOT; ?>/cure/importschure_v2" onsubmit="return form_sch()" target="_blank">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" id="date_sch" name="date_sch" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" />
            </div>
            <!-- <div class="form-group">
                <label for="shift">Shift</label>
                <select name="shift" id="shift" class="form-control" required>
                    <option value="day">กลางวัน</option>
                    <option value="night">กลางคืน</option>
                </select>
            </div> -->

            <button type="submit" id="view" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> ยืนยัน</button>

        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {

        $("#date_sch").datepicker({
            dateFormat: 'dd-mm-yy'
        });

    });
</script>