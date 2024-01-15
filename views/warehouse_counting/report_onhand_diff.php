<?php $this->layout("layouts/base", ["title" => "Stock Counting Report"]); ?>

<div class="head-space"></div>

<h1 class="text-center">Onhand Diff Report</h1>
<form action="/warehouse_counting/report_onhand" method="post" style="margin: 0 auto; max-width: 300px;">
<div style="max-width: 400px; margin: 0 auto;">
  <div class="text-center" style="margin-top: 30px;">
    <!-- <a href="/warehouse_counting/report/onhand/pdf" id="to_pdf" target="_blank" class="btn btn-danger btn-lg">View as PDF</a>
    <a href="/warehouse_counting/report/onhand/excel" id="to_excel" target="_blank" class="btn btn-success btn-lg">Export to Excel</a> -->
    <div class="form-group text-center" style="display: flex;">
      <button type="submit1" name="type_report" class="btn btn-danger btn-lg" value="pdf" id="to_pdf" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View as PDF</button>
      <button type="submit" name="type_report" class="btn btn-success btn-lg" style="margin-left:5px;" value="excel" id="to_excel"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Export to Excel</button>
    </div>
    </div>
</div>
<div class="text-center" style="margin-top:30px;">
  <div>
    <h4 class="text" style="display:inline">Year</h4>
    <select name="year" id="year">
    <option value="2023">2023</option>
    <option value="2022">2022</option>
    <option value="2021">2021</option>
    <option value="2020">2020</option>
    <option value="2019">2019</option>
    </select>
      
    <h4 class="text" style="display:inline;margin-left:10px;">Type</h4>
    <select name="group" id="group">
    <option value="RDT">PCR</option>
    <option value="TBR">TBR</option>    
    </select>
  </div>
</div>
</form>
