<?php $this->layout("layouts/base", ["title" => "Unbom"]); ?>

<div class="head-space"></div>


<div class="panel panel-default" style="margin: auto; max-width: 500px;">
    <div class="panel-heading">Unbom</div>
    <div class="panel-body">
        <form id="form_unbom_barcode">
            <div class="form-group">
                <label>Barcode</label>
                <input type="text" class="form-control inputs" name="barcode" id="barcode">
            </div>
        </form>
    </div>
</div>

<!-- Modal Authorize -->
<div class="modal" id="modal-authorize" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	Close
        </button>
        <h4 class="modal-title">Authorize</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <form id="form_authorize">
            <div class="form-group">
                <label>Authorize Code</label>
                <input type="text" class="form-control inputs" name="authorize_code" id="authorize_code">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control inputs" name="password" id="password">
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
    jQuery(document).ready(function($) {

        $('#modal_alert').on('hidden.bs.modal', function() {
            $(onFocus).focus();
        });

        localStorage.removeItem('authorize_unbom');
        localStorage.removeItem('authorize_by');

        var state_authorize = localStorage.getItem('authorize_unbom');

        if ( state_authorize === null || state_authorize !== 'passed') {
            $('#modal-authorize').modal({backdrop: 'static'});
            $('#barcode').prop('readonly', true);
            $('#authorize_code').focus();
        } else {
            $('#modal-authorize').modal('hide');
            $('#barcode').prop('readonly', false).focus();
        }

        $('#barcode').keydown(function(event) {
            if (event.which === 13) {
                var _barcode = $('#barcode').val();
                gojax('post', '/api/v1/unbom/save', {
                    barcode: $('#barcode').val(),
                    authorize: localStorage.getItem('authorize_by')
                }).done(function(data) {
                    if ( data.result === true ) {
                        // alert('Unbom Successful!');
                        $('#top_alert').show();
                        $('#modal_alert').modal('hide');
                        $('#top_alert_message').text('Barcode ล่าสุด : ' + _barcode);
                        // $('#form_unbom_barcode').trigger('reset');
                        $('#barcode').val('').focus();
                    } else {
                         $('#top_alert').hide();
                    $('#modal_alert').modal({backdrop: 'static'});
                    $('#modal_alert_message').text(data.message);
                    // $('#form_unbom_barcode').trigger('reset');
                    $('#barcode').val('');
                    onFocus = '#barcode';
                    }
                });
            }
        });

        $('#form_unbom_barcode').submit(function(event) {
            event.preventDefault();
        });

        $('#password').keydown(function(event) {
            if (event.which === 13) {
                $("#form_authorize").submit();
            }
        });

        $("#form_authorize").submit(function(e) {
            e.preventDefault();
            gojax('post', '/api/v1/authorize/is_authorize', {
                code: $('#authorize_code').val(),
                password: $('#password').val()
            }).done(function(data) {
                if ( data.result === true ) {
                    $('#modal-authorize').modal('hide');
                    $('#barcode').prop('readonly', false);
                    localStorage.setItem('authorize_unbom', 'passed');
                    localStorage.setItem('authorize_by', $('#authorize_code').val());
                    $('#barcode').val('').focus();
                } else {
                    alert(data.message);
                    $('#barcode').val('').focus();
                }
            });
        });
    });
</script>