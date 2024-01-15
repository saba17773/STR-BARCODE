<?php $this->layout("layouts/base", ['title' => 'WIP Final FG']); ?>
<h1 class="head-text">WIP Final FG. Report</h1>
<hr>


<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo APP_ROOT; ?>/api/pdf/wipfinalfg" target="_blank">
      <div class="form-group">
              <label for="BOI">BOI</label><br>
              <select name="selectMenuBOI[]" id="selectMenuBOI" multiple="multiple"   style="width: 370px" >
              </select>
      </div>

   <div class="form-group" style="display: block;">
     <strong>Type : </strong>
     <label style="padding-left: 40px;">
       <input type="radio" name="item_group" value="tbr" /> TBR
     </label>
     <label style="padding-left: 40px;">
       <input type="radio" name="item_group" value="pcr" /> PCR
     </label>

     <input type="hidden" name="check" id="check" />

   </div>

   <!-- <button type="button" class="btn btn-primary btn-lg btn-block" id="to_pdf"  name="to_pdf"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
   <button type="button" class="btn btn-primary btn-lg btn-block" id="to_excel"  name="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button> -->
   <!-- <button type="button" class="btn btn-success btn-xl " id="to_excel"  name="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button> -->
   <button type="button" class="btn btn-primary btn-lg " id="to_pdf" name="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>
   <button type="button" class="btn btn-success btn-lg " id="to_excel" name="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export excel</button>

 </form>
  </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
//  $( "#date_internal" ).datepicker({dateFormat: 'dd-mm-yy'});
  $('#selectMenuBOI').html("");
  $('#selectMenuBOI').multipleSelect({single: true});
  getPressSideBOI()
      .done(function(data) {
        $('#selectMenuBOI').append('<option value="1">ALL</option>');
        $.each(data, function(k, v) {
          $('#selectMenuBOI').append('<option value="'+ v.ID +'">'+v.ID+'</option>');
        });
          $('#selectMenuBOI').multipleSelect({single: true});
      });
});


$('#to_pdf').on('click', function(event) {
  event.preventDefault();
  var check =   $('input[name=check]').val(1);
  var checked = $('input[name=check]').val();
//  alert(checked);
    if(checked == 1)
    {
      $( "#form_internal" ).submit();
    }
    else {
      alert('ไม่สามารถเรียกReportได้');
    }



});

$('#to_excel').on('click', function(event) {
  event.preventDefault();
  var check =   $('input[name=check]').val(2);
  var checked = $('input[name=check]').val();
  //alert(checked);
    if(checked == 2)
    {
      $( "#form_internal" ).submit();
    }
    else {
      alert('ไม่สามารถเรียกReportได้');
    }



});
function getPressSideBOI() {
  return $.ajax({
    url : base_url + '/api/press/allBOI',
    type : 'get',
    dataType : 'json',
    cache : false
  });
}


// $('#to_pdf').on('click', function(event) {
//   event.preventDefault();
//   $('#to_excel').attr("disabled", true);
//   setTimeout(function () {
//   $('#to_excel').attr("disabled", false);
//   }, 10000);
//   //var item_group = $('input[name=item_group]').val();
//    var product_group = $('input[name=item_group]:checked').val();
//   var check =1;
//   alert(product_group);
//
//     if (!!product_group) {
//       //alert(1234);
//   $('#form_internal').submit(window.open(base_url+'/api/pdf/wipfinalfg', '_blank'));
//   // 	});
// }
//     });

  //   $('#to_excel').on('click', function(event) {
  //     event.preventDefault();
  //     $('#to_pdf').attr("disabled", true);
  //     setTimeout(function () {
  //     $('#to_pdf').attr("disabled", false);
  //     }, 10000);
  //     var date_scrap = $('#date_scrap').val();
  //     var product_group = $('input[name=item_group]:checked').val();
  //       var check =2;
  //       if (!!date_scrap) {
  // ('#formDateScrap').submit(window.open(base_url+'/report/curetire/scrap/'+date_scrap+'/'+product_group+'/'+check, '_blank'));
  //
  //   }
  //       });

</script>
