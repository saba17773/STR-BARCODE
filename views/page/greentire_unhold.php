<?php $this->layout("layouts/base", ['title' => 'Unhold']); ?>


  <h1 align='center'>Greentire UnHold</h1>

<div class="modal" id="dialog_checkuser" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Check User</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
            <table cellpadding="2">
            <tr>
                <td><label>Authorize Code :</label></td>
                <td><input type="text"  name="userbarcode" id='userbarcode'  class='form-control'></td>
            </tr>
            <tr>
                <td><button id='checkuser_save' class='btn btn-primary'>OK</button></td>
            </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="dialog_password" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Password</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
            <table cellpadding="2">
            <tr>
                <td><label>Password :</label></td>
                <td><input type="text"  name="password" id='password' class='form-control'></td>
            </tr>
            <tr>
                <td><button id='password_save' class='btn btn-primary'>OK</button></td>
                <td><button id='password_clear' class='btn btn-primary'>Clear</button></td>
            </tr>
        </table>
      </div>
    </div>
</div>
<div class="modal" id="dialog_barcode" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Barcode</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
            <table cellpadding="2">
            <tr>
                <td><label>Barcode :</label></td>
                <td><input type="text"  name="barcode" id='barcode' class='form-control'></td>
            </tr>
            <tr>
                <td><button id='barcode_save' class='btn btn-primary'>OK</button></td>
            </tr>
        </table>
      </div>
    </div>
</div>
<script type="text/javascript">
    
    jQuery(document).ready(function($) {
        init();  
       
    });

    function init(){
        $('#dialog_checkuser').modal('show');
    }
    $('#password_clear').bind('click',function(){
        $('#password').val('');
    });
    $("#checkuser_save").bind('click',function(){
        $.ajax({
            url : base_url + '/api/checkuserunhold',
            type : 'post',
            data : {
                userbarcode  : $('#userbarcode').val()
            },
            success : function(data){
                if(data==1)
                {
                    $('#dialog_checkuser').hide();
                    $('#dialog_password').modal('show');
                }
                else
                {
                    alert(data);
                }
               
            },
            error : function(data){
               alert(data);
            }
        });
    });

    $('#password_save').bind('click',function()
    {
        checkpass();
    });
    function checkpass()
    {
        
        $.ajax({
            url : base_url + '/api/checkpasswordunhold',
            type : 'post',
            dataType : 'json',
            cache : false,
            data :{
                userbarcode  : $('#userbarcode').val(),
                check        : $('#password').val()
            }
        })
        .done(function(data) {
           
           if(data==1)
           {
                $('#dialog_barcode').modal('show');
           }
           else
           {
             alert(data);
             $('#password').val('');
           }
        });

    }

    $('#barcode_save').bind('click',function()
    {
        checkbarcode();
    });

    function checkbarcode()
    {
       
        $.ajax({
            url : base_url + '/api/greentireunhold',
            type : 'post',
            dataType : 'json',
            cache : false,
            data :{
                barcode  : $('#barcode').val(),
                tostatus : 1,
                disposalid : 1
            }
        })
        .done(function(data) {
             alert(data);
             $('#barcode').val('');
        });

    }
</script>

