<?php $this->layout("layouts/base", ['title' => 'Add User']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<p id="demo"></p>

<div class="head-space"></div> 

<div class="panel panel-default" id="panel_build" style="max-width: 500px; margin: auto;" >
<div class="panel-heading">Building MC.</div>
  <div class="panel-body">
    <form id="form_BuildMc">
    
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="build_mc" placeholder="Barcode" autocomplete="off" autofocus>
			</div>
      <button type="submit" id="btnSubmitMC" class="btn btn-block btn-lg btn-primary" style="max-width: 500px; margin: auto;">
      <span class="glyphicon glyphicon-log-in"></span>
      ยืนยัน
      </button>
		</form>
	</div>
</div>

<div class="panel panel-default" id="panel_input">
  <div class="panel-heading">เพิ่มพนักงาน</div>
  <div class="panel-body">
    <div class="btn-panel">
      <button class="btn btn-success" id="Add" style="font-size: 1.3em;"><span class="glyphicon glyphicon-plus"></span> Add</button>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <button class="btn btn-danger" id="logout" style=" font-size: 1.3em;"><span class="glyphicon glyphicon-log-out"></span> Log Out</button>
    </div>
    <div id="grid_user"></div>
  </div>


  <div class="modal" id="modal_add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Add User</h4>
        </div>
        <div class="modal-body">
          <form id="form_desktop_login">
            <div class="form-group">
            <label for="username_login">Username</label>
            <input type="text" class="form-control input-lg" name="username_login" id="username_login"
            autocomplete="off" placeholder="ชื่อผู้ใช้" required autofocus>
            </div>
            <div class="form-group">
            <label for="password_login">Password</label>
            <input type="password" class="form-control input-lg" name="password_login" id="password_login"
            autocomplete="off" placeholder="รหัสผ่าน" required>
            </div>
            <div class="form-group">
              <label class="radio-inline">
              <input type="radio" name="build_type" id="build_type_1"  value="1" style="width: 1.5em; height: 1.5em;">
              <span style="padding-left: 10px; font-size: 1.4em;">Builder</span>
              </label>
              <label class="radio-inline">
              <input type="radio" name="build_type" id="build_type_2"  value="2" style="width: 1.5em; height: 1.5em;">
              <span id="name_type_2" style="padding-left: 10px; font-size: 1.4em;">Change Code</span>
              </label>
            </div>
              <button type="submit" id="btn_login" class="btn btn-block btn-lg btn-primary">
              <span class="glyphicon glyphicon-log-in"></span>
              เข้าสู่ระบบ
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="modal_logout" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"> Logout User : ชื่อ :
            <span id="name_logout" ></span>
          </h4> 
        </div>
        <div class="modal-body">
          <form id="form_logout">
            <div class="form-group">
              <label for="username_logout">Username</label>
              <input type="text" class="form-control input-lg" name="username_logout" id="username_logout"
              autocomplete="off" placeholder="ชื่อผู้ใช้" required autofocus>
            </div>
            <div class="form-group">
              <label for="password_logout">Password</label>
              <input type="password" class="form-control input-lg" name="password_logout" id="password_logout"
              autocomplete="off" placeholder="รหัสผ่าน" required>
            </div>
            <div class="form-group">
            <input type="hidden" class="form-control input-lg" name="id" id="id" required>
            </div>
              <button type="submit" id="btn_logout" class="btn btn-block btn-lg btn-primary">
              <span class="glyphicon glyphicon-log-in"></span>
              ออกจากระบบ
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  

<script>
	jQuery(document).ready(function($) {  

    var sessionBuild = '<?php echo $_SESSION['build_mc']; ?>';
    if (sessionBuild == ""){
      $('#panel_build').show();
      $("#panel_input").hide();
    }else{
      $('#panel_build').hide();
      $("#panel_input").show();

      grid_user();
    }

    var myVar = setInterval(myTimer, 60000);

    function myTimer() 
    {
      gojax_f('post', base_url+'/api/User/firstRows_Session/'+ sessionBuild)
      .done(function(data) {
        if (data.status == 404) 
        {
          window.location = base_url + '/addUser';
        }
      });
    }

    $('#form_BuildMc').on('submit', function(event)
    {
      var build_mc = $('input[name=build_mc]').val();
      event.preventDefault();

      gojax_f('post', base_url+'/api/User/chkUserLogin', '#form_BuildMc')
      .done(function(c){
        if(c.status == 200) 
        {
          gojax_f('post', base_url+'/api/User/insertRate/' + build_mc, '#form_BuildMc')
          .done(function(data){ 
            if(data.status == 200){
              grid_user();
              $('#panel_build').hide();
              $("#panel_input").show();
              
            } else {
              $('#modal_alert').modal({backdrop: 'static'});
				      $('#modal_alert_message').text(data.message);
              $('input[name=build_mc]').val('');
            }
          });
        }
        else 
        {
          $("#dialog-confirm").html(c.message);
          $( "#dialog-confirm" ).dialog({
            resizable: false,
            title: "ยืนยันการออกจากระบบ",
            height: "auto",
            width: 600,
            modal: true,
            buttons: {
              "Yes": function() 
              {

                gojax_f('post', base_url+'/api/User/logoutRequest', '#form_desktop_login')
                .done(function(chk) {
                  if (chk.status == 200) 
                  {
                    gojax_f('post', base_url+'/api/User/insertRate/' + build_mc, '#form_BuildMc')
                    .done(function(data){ 
                      if(data.status == 200){
                        grid_user();
                        $('#panel_build').hide();
                        $("#panel_input").show();
                      } else {
                        $('#modal_alert').modal({backdrop: 'static'});
				                $('#modal_alert_message').text(data.message);
                      }
                    });
                  }
                  else
                  {
                    $('#modal_alert').modal({backdrop: 'static'});
				            $('#modal_alert_message').text(chk.message);
                  }
                });
                $( this ).dialog( "close" );
              },
              Cancel: function() 
              {
                $( this ).dialog( "close" );
              }
            }
          });
        }
      });
      
    });
    
    $('#Add').on('click', function() {
			var dataCount = $('#grid_user').jqxGrid('getrows');  
      var len=dataCount.length; 

      gojax_f('post', base_url+'/api/User/chkTypeMC')
      .done(function(data) {
        if (data.message == "TBR") 
        {
          
          if (len < 4) 
          {
            $('#modal_add').modal({backdrop: 'static'});
            $("#form_desktop_login")[0].reset();
            $("[name=build_type]").filter("[value='1']").attr("checked","checked");
            $('#build_type_2').show();
            $('#name_type_2').show();
          } else {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text('จำนวนผู้ใช้มากกว่าจำนวนที่กำหนด กรุณาออกจากระบบก่อน');
          }
        }else if (data.message == "PCR" && (data.mc == "VMI01"||data.mc == "VMI02") )
        {
          if (len < 4) 
          {
            $('#modal_add').modal({backdrop: 'static'});
            $("#form_desktop_login")[0].reset();
            $("[name=build_type]").filter("[value='1']").attr("checked","checked");
            $('#build_type_2').show();
            $('#name_type_2').show();
          } else {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text('จำนวนผู้ใช้มากกว่าจำนวนที่กำหนด กรุณาออกจากระบบก่อน');
          }
        } else if (data.message == "PCR")
        {
          
          if (len < 3) 
          {
            $('#modal_add').modal({backdrop: 'static'});
            $("#form_desktop_login")[0].reset();
            $("[name=build_type]").filter("[value='1']").attr("checked","checked");
            $('#build_type_2').hide();
            $('#name_type_2').hide();
          } else {
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text('จำนวนผู้ใช้มากกว่าจำนวนที่กำหนด กรุณาออกจากระบบก่อน');
          }
        }
      });

			// if (len < 4) 
      // {
      //   $('#modal_add').modal({backdrop: 'static'});
		  // 	$("#form_desktop_login")[0].reset();
      //   $("[name=build_type]").filter("[value='1']").attr("checked","checked");
			// } else {
			// 	$('#modal_alert').modal({backdrop: 'static'});
			// 	$('#modal_alert_message').text('จำนวนผู้ใช้มากกว่าจำนวนที่กำหนด กรุณาออกจากระบบก่อน');
			// }
		});

    $('#logout').on('click',function(){
      var rowdata = row_selected('#grid_user');
			if (typeof rowdata !== 'undefined') {
        $('#modal_logout').modal({backdrop: 'static'});
        $("#form_logout")[0].reset();
        $('#id').val(rowdata.Id);
        $('#name_logout').text(rowdata.Name);
        
      } else {
        $('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
      }
    });

    $('#form_desktop_login').on('submit', function(event) {
				
        $('#modal_add').modal('hide');
        event.preventDefault();
      
        var username = $('input[name=username_login]').val();
        var password = $('input[name=password_login]').val();
        var type = $("input[name=build_type]:checked").val();
        
        
        gojax_f('post', base_url+'/api/User/chkBuildType/' + type , '#form_desktop_login')
        .done(function(data) {
          if (data.status == 404) {
            $('#modal_alert').modal({backdrop: 'static'});
				    $('#modal_alert_message').text(data.message);
          } 
          else 
          {
            gojax_f('post', base_url+'/api/User/chkUserLogin2/'+ username, '#form_desktop_login')
            .done(function(c)
            { 
              if(c.status == 200) 
              { 
                gojax_f('post', base_url+'/api/User/insertMore/'+ username +'/'+ password +'/'+ type, '#form_desktop_login')
                .done(function(data) {
                  if (data.status == 404) 
                  {
                    $('#modal_alert').modal({backdrop: 'static'});
				            $('#modal_alert_message').text(data.message);
                  } else 
                  {
                    $('#grid_user').jqxGrid('updatebounddata');
                  }
                });

              } 
              else 
              {
                gojax_f('post', base_url+'/api/User/getMachine2/'+ username , '#form_desktop_login')
                .done(function(mc) 
                {
                  //กรณี Login ซ้ำในเครื่องเดิม
                  if (mc.status == 404) 
                  {
                    $('#modal_alert').modal({backdrop: 'static'});
				            $('#modal_alert_message').text(mc.message);
                  } 
                  else 
                  {
                    //กรณีที่มีการ Login ค้างไว้ที่เครื่องอื่น
                    $("#dialog-confirm").html(c.message);
                    $( "#dialog-confirm" ).dialog({
                      resizable: false,
                      title: "ยืนยันการออกจากระบบ",
                      height: "auto",
                      width: 600,
                      modal: true,
                      buttons: {
                        "Yes": function() 
                        {
                          // ไป update LogoutDate 
                          gojax_f('post', base_url+'/api/User/logoutRequest2', '#form_desktop_login')
                          .done(function(chk) {
                            if (chk.status == 200) 
                            {
                              //ไปเช็คว่ามียอดค้างอยู่อีกไหม ถ้ามี ให้ insert แบบ เอาค่าเดิมมาด้วย
                              gojax_f('post', base_url+'/api/User/insertMore/'+ username +'/'+ password +'/'+ type, '#form_desktop_login')
                              .done(function(data) {
                                if (data.status == 404) 
                                {
                                  $('#modal_alert').modal({backdrop: 'static'});
				                          $('#modal_alert_message').text(data.message);
                                } 
                                else 
                                {
                                  $('#grid_user').jqxGrid('updatebounddata');
                                }
                              });
                            } 
                            else 
                            {
                              $('#modal_alert').modal({backdrop: 'static'});
				                      $('#modal_alert_message').text(chk.message);
                            }
                          });

                          $( this ).dialog( "close" );
                        },
                        Cancel: function() 
                        {
                          $( this ).dialog( "close" );
                        }
                      }
                    });
                  }
                });
              }
            });
          }
        });
    });

    $('#form_logout').on('submit', function(event) {
      
        $('#modal_logout').modal('hide');
        event.preventDefault();
  
        var user = $('input[name=username_logout]').val();
        var pass = $('input[name=password_logout]').val();
        var id = $('input[name=id]').val();
        
        $("#dialog-confirm").html("คุณต้องการออกจากระบบหรือไม่ ?");
        $( "#dialog-confirm" ).dialog({
          resizable: false,
          text:"test",
          height: "auto",
          width: 600,
          modal: true,
          buttons: {
            "Yes": function() {
              gojax_f('post', base_url+'/api/User/updateLogoutDate/'+ user +'/'+ pass+ '/'+ id, '#form_logout')
              .done(function(c){ 
                if(c.status == 200) { 
                  $('#grid_user').jqxGrid('updatebounddata');
                }
                else if (c.status == 400){
                  window.location = base_url + c.redirectTo;
                }
                else {
                  
                  $('#modal_alert').modal({backdrop: 'static'});
				          $('#modal_alert_message').text(c.message);

                  //window.location = base_url + c.redirectTo;
                }
                
              });
              
              $( this ).dialog( "close" );
              
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
        
    });

    function grid_user() {
      var dataAdapter = new $.jqx.dataAdapter({
        datatype: 'json',
        datafields: [
          { name: 'UserId', type: 'int'},
          { name: 'Machine', type: 'string'},
          { name: 'LoginDate', type: 'string' },
          { name: 'Name', type: 'string'},
          { name: 'Role', type: 'string'},
          { name: 'Id', type: 'int'},
          { name: 'BuildType', type: 'int'}
        ],
        url: base_url + "/api/User/ShowUser"
      });
      return $("#grid_user").jqxGrid({
        width: '100%',
        source: dataAdapter,
        autoheight: true,
        pageSize : 10,
        // rowsheight : 40,
        // columnsheight : 40,
        altrows : true,
        pageable : true,
        sortable: true,
        filterable : true,
        showfilterrow : true,
        columnsresize: true,
        // theme : 'theme',
        columns: [
          { text: 'รหัส', datafield: 'UserId', width: 100},
          { text: 'ชื่อ - นามสกุล', datafield: 'Name', width: 200},
          { text: 'เครื่อง', datafield: 'Machine', width: 80},
          { text: 'วันและเวลาที่เข้าสู่ระบบ', datafield: 'LoginDate', width: 250},
          { text: 'หน้าที่', datafield: 'BuildType', width: 100}
        ]
      });
    }

  });
</script>