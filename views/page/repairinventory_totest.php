
    <!-- Excel -->
    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <td style="text-align: center;" colspan="3">
                    <img src="./assets/images/str.jpg" width="150" alt="">
                </td>
                <td style="text-align: center; padding: 30px;" colspan="5">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Repair Inventory Report</div>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    Product Group : <?php echo $product_group; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BOI : <?php echo $BOIName; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    Export Date : <?php echo date("d-m-Y H:i:s"); 
                    ?>
                    
                </td>
            </tr>
                <tr>
                <th style="border-top: 0px; padding: 10px; width:20px;">No.</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">Press No.</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">GT Code</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">Cure Code</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">MC Build</th>
                <th style="border-top: 0px; padding: 10px; width:150px;">Date Repiar</th>
                <th style="border-top: 0px; padding: 10px; width:300px;">Defect Discription</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">Weekly</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $i=1;
             foreach ($data as $key => $value)
             {
                echo "<tr>";
                echo "<td align='center' style='width:5px;'>" . $i . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["PressNo"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["GT_Code"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["CuringCode"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["BuildingNo"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . date('d-m-Y  H:i:s', strtotime($value["DateRepair"])) . "</td>";
                echo "<td align='left' style='width:5px;'>" . $value["Description"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["Batch"] . "</td>";
                echo "</tr>";
                $i++;
             }
            ?>
            <tr>
        </tbody>
    </table>

    <!-- PDF -->
    <table border="1" cellspacing="0" width="100%">
        <thead>
            <tr>
                <td style="text-align: center;" colspan="3">
                    <img src="./assets/images/str.jpg" width="150" alt="">
                </td>
                <td style="text-align: center; padding: 30px;" colspan="5">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Repair Inventory Report</div>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    Product Group : <?php echo $product_group; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    BOI : <?php echo $BOIName; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    Export Date : <?php echo date("d-m-Y H:i:s"); 
                    ?>
                    
                </td>
            </tr>
                <tr>
                <th style="border-top: 0px; padding: 10px; width:20px;">No.</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">Press No.</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">GT Code</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">Cure Code</th>
                <th style="border-top: 0px; padding: 10px; width:30px;">MC Build</th>
                <th style="border-top: 0px; padding: 10px; width:150px;">Date Repiar</th>
                <th style="border-top: 0px; padding: 10px; width:300px;">Defect Discription</th>
                <th style="border-top: 0px; padding: 10px; width:100px;">Weekly</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $i=1;
             foreach ($data as $key => $value)
             {
                echo "<tr>";
                echo "<td align='center' style='width:5px;'>" . $i . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["PressNo"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["GT_Code"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["CuringCode"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["BuildingNo"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . date('d-m-Y  H:i:s', strtotime($value["DateRepair"])) . "</td>";
                echo "<td align='left' style='width:5px;'>" . $value["Description"] . "</td>";
                echo "<td align='center' style='width:5px;'>" . $value["Batch"] . "</td>";
                echo "</tr>";
                $i++;
             }
            ?>
            <tr>
        </tbody>            
    </table>